// "Suggest with AI" buttons on admin forms.
//
// A button looks like this:
//   <button type="button" class="dre-ai-btn" data-ai-task="meta_description" data-ai-target="#fieldId"
//           data-ai-title="#nameField" data-ai-text="#editorId,#otherField" data-ai-page="Service page">
//
// The suggestion is shown under the field. It goes into the field only when the person clicks
// "Use this", and it is saved only when they save the form themselves.
(function () {
	'use strict';

	var settings = window.DRE_AI_ASSIST;
	if (!settings || !settings.endpoint || !window.fetch) {
		return;
	}

	// The text of a field, also when it is a rich-text editor.
	function valueOf(selector) {
		var field = selector ? document.querySelector(selector) : null;
		if (!field) {
			return '';
		}
		var editor = window.tinymce && field.id ? window.tinymce.get(field.id) : null;
		return editor ? editor.getContent() : (field.value || '');
	}

	function valuesOf(selectors) {
		return (selectors || '').split(',').map(function (selector) { return valueOf(selector.trim()); }).filter(function (text) { return text.trim() !== ''; }).join('\n\n');
	}

	// "Line one\n\n- item" -> paragraphs and a list, for a rich-text editor.
	function toHtml(text) {
		var holder = document.createElement('div');
		var list = null;
		text.split(/\n+/).forEach(function (line) {
			line = line.trim();
			if (line === '') {
				return;
			}
			var item = /^[-*•]\s+(.*)$/.exec(line);
			if (item) {
				if (!list) {
					list = document.createElement('ul');
					holder.appendChild(list);
				}
				var li = document.createElement('li');
				li.textContent = item[1];
				list.appendChild(li);
			} else {
				list = null;
				var p = document.createElement('p');
				p.textContent = line;
				holder.appendChild(p);
			}
		});
		return holder.innerHTML;
	}

	function put(selector, text) {
		var field = document.querySelector(selector);
		if (!field) {
			return;
		}
		var editor = window.tinymce && field.id ? window.tinymce.get(field.id) : null;
		if (editor) {
			editor.setContent(toHtml(text));
			editor.fire('change');
			editor.save();
		} else {
			field.value = text;
		}
		// Lets the form update its counters, previews and "unsaved changes" note.
		field.dispatchEvent(new Event('input', { bubbles: true }));
		field.dispatchEvent(new Event('change', { bubbles: true }));
		field.focus();
	}

	function make(tag, className, text) {
		var node = document.createElement(tag);
		if (className) {
			node.className = className;
		}
		if (text !== undefined) {
			node.textContent = text;
		}
		return node;
	}

	function panelFor(button) {
		var id = button.getAttribute('data-ai-panel');
		var panel = id ? document.getElementById(id) : null;
		if (!panel) {
			panel = make('div', 'dre-ai-panel');
			panel.id = 'dreAiPanel' + Math.random().toString(36).slice(2, 9);
			panel.setAttribute('role', 'status');
			panel.setAttribute('aria-live', 'polite');
			button.setAttribute('data-ai-panel', panel.id);
			var holder = button.closest('.dre-field') || button.parentNode;
			holder.appendChild(panel);
		}
		return panel;
	}

	function ask(button, previous) {
		var panel = panelFor(button);
		var target = button.getAttribute('data-ai-target');
		var label = button.getAttribute('data-ai-label') || button.textContent.trim();
		panel.textContent = '';
		panel.hidden = false;
		panel.appendChild(make('span', 'dre-ai-wait', 'Writing a suggestion...'));
		button.disabled = true;

		window.fetch(settings.endpoint, {
			method: 'POST',
			credentials: 'same-origin',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify({
				csrf: settings.csrf,
				task: button.getAttribute('data-ai-task'),
				title: valueOf(button.getAttribute('data-ai-title')),
				text: valuesOf(button.getAttribute('data-ai-text')),
				current: valueOf(target),
				page: button.getAttribute('data-ai-page') || '',
				previous: previous || ''
			})
		}).then(function (response) {
			return response.json().catch(function () { return { ok: false, message: 'The answer could not be read. Please try again.' }; });
		}).catch(function () {
			return { ok: false, message: 'The request did not go through. Check the connection and try again.' };
		}).then(function (data) {
			button.disabled = false;
			panel.textContent = '';
			if (!data.ok) {
				panel.appendChild(make('span', 'dre-ai-error', data.message || 'No suggestion came back. Please try again.'));
				var close = make('button', 'dre-btn', 'Close');
				close.type = 'button';
				close.addEventListener('click', function () { panel.hidden = true; button.focus(); });
				panel.appendChild(close);
				return;
			}
			panel.appendChild(make('strong', 'dre-ai-title', 'Suggestion' + (data.characters ? ' (' + data.characters + ' characters)' : '')));
			// Shown as text, never as HTML.
			panel.appendChild(make('p', 'dre-ai-text', data.text));
			panel.appendChild(make('small', '', 'Written by AI from what is on this form. Read it and correct it before you use it.'));
			var buttons = make('div', 'dre-ai-actions');
			var use = make('button', 'dre-btn dre-btn-main', 'Use this');
			use.type = 'button';
			use.addEventListener('click', function () {
				put(target, data.text);
				panel.hidden = true;
			});
			var again = make('button', 'dre-btn', 'Try another');
			again.type = 'button';
			again.addEventListener('click', function () { ask(button, data.text); });
			var dismiss = make('button', 'dre-btn', 'Close');
			dismiss.type = 'button';
			dismiss.addEventListener('click', function () { panel.hidden = true; button.focus(); });
			buttons.appendChild(use);
			buttons.appendChild(again);
			buttons.appendChild(dismiss);
			panel.appendChild(buttons);
			use.focus();
		});
	}

	document.addEventListener('click', function (event) {
		var button = event.target.closest('.dre-ai-btn');
		if (button && !button.disabled) {
			event.preventDefault();
			ask(button, '');
		}
	});
})();
