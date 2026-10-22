// SEO check for the project forms: works like the Yoast SEO box in WordPress.
//
// It reads the form while the person types (name, page address, meta title, meta description,
// the text editors and the picture descriptions), checks it against a focus keyphrase, and shows
// red / orange / green results in two lists: SEO and readability. Nothing is sent anywhere;
// the two scores travel with the form when it is saved, so the project list can show them.
(function () {
	'use strict';

	var root = document.getElementById('dreSeo');
	var form = root ? root.closest('form') : null;
	if (!root || !form) {
		return;
	}

	// ---------------------------------------------------------------
	// Reading the form
	// ---------------------------------------------------------------
	var EDITORS = ['proj_overview', 'proj_fea_fac_cont', 'proj_loc_cont', 'proj_masterplan', 'content', 'serv_sec_content'];
	var ALT_FIELDS = ['proj_dev_img_alt', 'proj_loc_img_alt_tag', 'proj_mastrpln_img_alt_tag', 'og_image'];

	function field(name) {
		return form.querySelector('[name="' + name + '"]');
	}
	function value(name) {
		var input = field(name);
		if (!input) {
			return '';
		}
		var editor = window.tinymce && input.id ? window.tinymce.get(input.id) : null;
		return (editor ? editor.getContent() : input.value || '').trim();
	}
	function slugify(text) {
		return text.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
	}

	// ---------------------------------------------------------------
	// Working with text
	// ---------------------------------------------------------------
	function plain(text) {
		return text.toLowerCase().replace(/[’']/g, '').replace(/[^a-z0-9À-ɏ؀-ۿ]+/g, ' ').trim();
	}
	function wordsOf(text) {
		var clean = plain(text);
		return clean === '' ? [] : clean.split(' ');
	}
	function sentencesOf(text) {
		return text.replace(/\s+/g, ' ').split(/(?<=[.!?])\s+(?=[A-Z0-9“"'(])/).map(function (sentence) { return sentence.trim(); }).filter(function (sentence) { return wordsOf(sentence).length > 0; });
	}
	// Little words that do not have to match: "villas for sale in dubai" is found in "villas in Dubai on sale".
	var FILLER = ['a', 'an', 'the', 'in', 'at', 'on', 'of', 'for', 'to', 'by', 'and', 'or', 'with', 'from'];
	function keyWords(phrase) {
		var all = wordsOf(phrase);
		var strong = all.filter(function (word) { return FILLER.indexOf(word) === -1; });
		return strong.length ? strong : all;
	}
	function hasPhrase(text, phrase) {
		var haystack = ' ' + plain(text) + ' ';
		if (haystack.indexOf(' ' + plain(phrase) + ' ') !== -1) {
			return true;
		}
		var needed = keyWords(phrase);
		return needed.length > 0 && needed.every(function (word) { return haystack.indexOf(' ' + word + ' ') !== -1; });
	}
	function countPhrase(sentences, phrase) {
		return sentences.filter(function (sentence) { return hasPhrase(sentence, phrase); }).length;
	}
	function syllables(word) {
		var groups = word.replace(/e$/, '').match(/[aeiouy]+/g);
		return Math.max(1, groups ? groups.length : 1);
	}
	function percent(part, whole) {
		return whole ? Math.round(part / whole * 100) : 0;
	}

	var TRANSITIONS = ['also', 'as a result', 'as well as', 'because', 'besides', 'but', 'consequently', 'finally', 'first', 'for example', 'for instance', 'furthermore', 'however', 'in addition', 'in fact', 'in short', 'instead', 'meanwhile', 'moreover', 'next', 'not only', 'otherwise', 'second', 'similarly', 'so', 'such as', 'that is why', 'then', 'therefore', 'thus', 'together with', 'what is more', 'whether', 'while', 'yet', 'additionally', 'as well', 'in particular', 'above all', 'after all', 'at the same time', 'thanks to', 'due to', 'so that', 'which means'];
	var PASSIVE = /\b(am|is|are|was|were|be|been|being)\s+(?:\w+ly\s+)?(\w+ed|built|made|given|known|done|seen|sold|set|found|held|kept|shown|taken|written|chosen|drawn|spread|run|laid)\b/i;

	// ---------------------------------------------------------------
	// Everything the checks look at, read once per run
	// ---------------------------------------------------------------
	var galleryAlts = [];
	try {
		galleryAlts = JSON.parse(root.getAttribute('data-gallery-alts') || '[]');
	} catch (error) {}

	function readPage() {
		var activeEditors = EDITORS.filter(function (ed) { return field(ed); });
		var holder = document.createElement('div');
		holder.innerHTML = (activeEditors.length ? activeEditors : EDITORS).map(value).join('\n');
		var first = document.createElement('div');
		first.innerHTML = activeEditors.length ? value(activeEditors[0]) : value(EDITORS[0]);
		var firstParagraph = first.querySelector('p, li, h2, h3');
		var paragraphs = Array.prototype.map.call(holder.querySelectorAll('p, li'), function (node) { return node.textContent.replace(/\s+/g, ' ').trim(); }).filter(function (text) { return text !== ''; });
		if (!paragraphs.length && holder.textContent.trim() !== '') {
			paragraphs = [holder.textContent.replace(/\s+/g, ' ').trim()];
		}
		var links = Array.prototype.map.call(holder.querySelectorAll('a[href]'), function (link) { return link.getAttribute('href') || ''; });
		var site = (root.getAttribute('data-url') || '').replace(/^https?:\/\//, '').split('/')[0];
		var name = value('proj_name') || value('name') || value('serv_sec_name');
		var metaTitle = value('meta_title') || value('title');
		var metaDesc = value('meta_description') || value('description');
		var slugVal = value('proj_slug') || value('slug') || value('serv_sec_slug');
		return {
			focus: value('seo_focus').replace(/\s+/g, ' '),
			name: name,
			title: metaTitle || name,
			hasOwnTitle: metaTitle !== '',
			description: metaDesc.replace(/\s+/g, ' '),
			slug: slugVal || slugify(name),
			text: paragraphs.join(' '),
			paragraphs: paragraphs,
			intro: firstParagraph ? firstParagraph.textContent : (paragraphs[0] || ''),
			headings: Array.prototype.map.call(holder.querySelectorAll('h1, h2, h3, h4'), function (node) { return node.textContent; }),
			// Each text box is shown under its own heading on the website (Overview, Amenities, Location, Master plan).
			longestPartWithoutHeading: Math.max.apply(null, (activeEditors.length ? activeEditors : EDITORS).map(function (name) {
				var part = document.createElement('div');
				part.innerHTML = value(name);
				return part.querySelector('h2, h3, h4') ? 0 : wordsOf(part.textContent).length;
			})),
			contentImages: Array.prototype.map.call(holder.querySelectorAll('img'), function (image) { return image.getAttribute('alt') || ''; }),
			alts: ALT_FIELDS.map(value).concat(galleryAlts).concat(Array.prototype.map.call(form.querySelectorAll('input[name^="gallery_alt"], input[name*="gallery_img_alt"]'), function (input) { return input.value.trim(); })),
			internalLinks: links.filter(function (href) { return /^\//.test(href) || (site !== '' && href.indexOf(site) !== -1) || /drehomes\.com/i.test(href); }).length,
			outboundLinks: links.filter(function (href) { return /^https?:\/\//i.test(href) && (site === '' || href.indexOf(site) === -1) && !/drehomes\.com/i.test(href); }).length
		};
	}

	// ---------------------------------------------------------------
	// The checks. Each returns [state, text]: state is "good", "ok" or "bad".
	// ---------------------------------------------------------------
	function seoChecks(page) {
		var results = [];
		var add = function (state, title, text) { results.push({ state: state, title: title, text: text }); };
		var focus = page.focus;
		var words = wordsOf(page.text);
		var sentences = sentencesOf(page.text);

		if (focus === '') {
			add('bad', 'Focus keyphrase', 'No focus keyphrase was set. Type the words this page should be found for, and the checks below are measured against it.');
		} else {
			var length = keyWords(focus).length;
			add(length <= 4 ? 'good' : (length <= 6 ? 'ok' : 'bad'), 'Keyphrase length', length <= 4 ? 'Good job!' : 'The keyphrase has ' + length + ' important words. Up to 4 works best; a shorter one is easier to use in the text.');

			var same = root.getAttribute('data-same') || '';
			if (same !== '' && plain(root.getAttribute('data-same-for') || '') === plain(focus)) {
				add('bad', 'Keyphrase used before', 'This keyphrase is also used on: ' + same + '. Two pages with the same keyphrase compete with each other; give this one its own.');
			} else {
				add('good', 'Keyphrase used before', 'No other project uses this keyphrase.');
			}

			if (!hasPhrase(page.title, focus)) {
				add('bad', 'Keyphrase in the SEO title', 'The title does not contain the keyphrase. Put it in, as near to the beginning as you can.');
			} else {
				var begins = plain(page.title).indexOf(keyWords(focus)[0]) <= 12;
				add(begins ? 'good' : 'ok', 'Keyphrase in the SEO title', begins ? 'The title contains the keyphrase near its beginning. Good job!' : 'The title contains the keyphrase, but not at the beginning. Move it forward if it still reads well.');
			}

			add(hasPhrase(page.description, focus) ? 'good' : (page.description === '' ? 'bad' : 'ok'), 'Keyphrase in the meta description', hasPhrase(page.description, focus) ? 'The keyphrase appears in the meta description. Well done!' : 'The meta description does not contain the keyphrase. Add it, so people recognise their search in the result.');

			var slugHit = hasPhrase(page.slug.replace(/-/g, ' '), focus);
			var slugSome = keyWords(focus).some(function (word) { return (' ' + page.slug.replace(/-/g, ' ') + ' ').indexOf(' ' + word + ' ') !== -1; });
			add(slugHit ? 'good' : (slugSome ? 'ok' : 'bad'), 'Keyphrase in the page address', slugHit ? 'The page address contains the keyphrase. Great!' : (slugSome ? 'Only part of the keyphrase is in the page address.' : 'The keyphrase is not in the page address. Mind that changing the address of a live page breaks old links to it.'));

			if (words.length === 0) {
				add('bad', 'Keyphrase in the introduction', 'There is no description yet.');
			} else {
				add(hasPhrase(page.intro, focus) ? 'good' : 'bad', 'Keyphrase in the introduction', hasPhrase(page.intro, focus) ? 'The first paragraph of the description contains the keyphrase. Well done!' : 'The first paragraph of the description does not contain the keyphrase. Make clear at once what the page is about.');

				var found = countPhrase(sentences, focus);
				var density = words.length ? found * keyWords(focus).length / words.length * 100 : 0;
				var wanted = Math.max(2, Math.round(words.length / 200));
				if (found === 0) {
					add('bad', 'Keyphrase in the text', 'The keyphrase was not found in the text. Use it about ' + wanted + ' times.');
				} else if (density > 3.5) {
					add('bad', 'Keyphrase in the text', 'The keyphrase was found ' + found + ' times, which is too often for a text of this length. It starts to read like spam; use other words here and there.');
				} else if (found < wanted) {
					add('ok', 'Keyphrase in the text', 'The keyphrase was found ' + found + (found === 1 ? ' time' : ' times') + '. For a text of this length, about ' + wanted + ' times is better.');
				} else {
					add('good', 'Keyphrase in the text', 'The keyphrase was found ' + found + ' times. That is great!');
				}
			}

			if (page.headings.length) {
				var inHeadings = page.headings.filter(function (heading) { return hasPhrase(heading, focus); }).length;
				add(inHeadings ? 'good' : 'ok', 'Keyphrase in subheadings', inHeadings ? inHeadings + ' of the ' + page.headings.length + ' subheadings reflect the keyphrase. Good job!' : 'None of the subheadings contains the keyphrase. Use it in one of them.');
			}

			var alts = page.alts.concat(page.contentImages).filter(function (alt) { return alt !== ''; });
			var altHits = alts.filter(function (alt) { return hasPhrase(alt, focus); }).length;
			add(altHits ? 'good' : (alts.length ? 'ok' : 'bad'), 'Keyphrase in picture descriptions', altHits ? altHits + ' picture ' + (altHits === 1 ? 'description contains' : 'descriptions contain') + ' the keyphrase. Good job!' : (alts.length ? 'None of the picture descriptions (alt text) contains the keyphrase.' : 'The pictures have no description (alt text) yet. Describe them, and use the keyphrase in at least one.'));
		}

		// Checks that do not need a keyphrase
		var titleLength = page.title.length;
		if (!page.hasOwnTitle) {
			add('ok', 'SEO title', 'No meta title was written, so the project name is used. A title written for search results usually does better.');
		} else if (titleLength < 30) {
			add('ok', 'SEO title length', 'The title has ' + titleLength + ' characters. That is short: use the room (up to 60) to add the area or the developer.');
		} else if (titleLength <= 60) {
			add('good', 'SEO title length', 'The title has ' + titleLength + ' characters. Good job!');
		} else {
			add(titleLength <= 70 ? 'ok' : 'bad', 'SEO title length', 'The title has ' + titleLength + ' characters. Google cuts it off after about 60.');
		}

		var descriptionLength = page.description.length;
		if (descriptionLength === 0) {
			add('bad', 'Meta description length', 'No meta description was written. Google will pick a piece of the page by itself, which is rarely the best one.');
		} else if (descriptionLength < 120) {
			add('ok', 'Meta description length', 'The meta description has ' + descriptionLength + ' characters. Up to 160 are shown; use the room.');
		} else if (descriptionLength <= 160) {
			add('good', 'Meta description length', 'The meta description has ' + descriptionLength + ' characters. Well done!');
		} else {
			add('ok', 'Meta description length', 'The meta description has ' + descriptionLength + ' characters. Google cuts it off after about 160.');
		}

		add(words.length >= 300 ? 'good' : (words.length >= 150 ? 'ok' : 'bad'), 'Text length', 'The texts of this page have ' + words.length + ' words. ' + (words.length >= 300 ? 'Good job!' : 'At least 300 words give Google enough to understand the page.'));
		add(page.internalLinks ? 'good' : 'ok', 'Links to other pages of this website', page.internalLinks ? 'The text links to ' + page.internalLinks + ' other ' + (page.internalLinks === 1 ? 'page' : 'pages') + ' of this website. Good job!' : 'The text has no link to another page of this website, such as the developer or the community. Add one.');
		add(page.outboundLinks ? 'good' : 'ok', 'Links to other websites', page.outboundLinks ? 'The text links to ' + page.outboundLinks + ' other ' + (page.outboundLinks === 1 ? 'website' : 'websites') + '.' : 'The text has no link to another website. One useful link, for example to the developer, is a plus but not a must.');
		return results;
	}

	function readabilityChecks(page) {
		var results = [];
		var add = function (state, title, text) { results.push({ state: state, title: title, text: text }); };
		var words = wordsOf(page.text);
		if (words.length < 30) {
			add('bad', 'Not enough text', 'There is too little text to judge how easy it is to read. Write the description first.');
			return results;
		}
		var sentences = sentencesOf(page.paragraphs.join('. ').replace(/\.\s*\./g, '.'));

		var long = sentences.filter(function (sentence) { return wordsOf(sentence).length > 20; }).length;
		var longShare = percent(long, sentences.length);
		add(longShare <= 25 ? 'good' : (longShare <= 30 ? 'ok' : 'bad'), 'Sentence length', longShare <= 25 ? 'Only ' + longShare + '% of the sentences have more than 20 words. Great!' : longShare + '% of the sentences have more than 20 words; 25% is the most that reads easily. Split the long ones.');

		var longParagraphs = page.paragraphs.filter(function (paragraph) { return wordsOf(paragraph).length > 150; }).length;
		add(longParagraphs === 0 ? 'good' : (longParagraphs === 1 ? 'ok' : 'bad'), 'Paragraph length', longParagraphs === 0 ? 'None of the paragraphs is too long. Great job!' : longParagraphs + (longParagraphs === 1 ? ' paragraph has' : ' paragraphs have') + ' more than 150 words. Break them up.');

		if (words.length > 300) {
			var longPart = page.longestPartWithoutHeading;
			add(longPart <= 300 ? 'good' : (longPart <= 450 ? 'ok' : 'bad'), 'Subheadings', longPart <= 300 ? 'No part of the text runs on for more than 300 words without a heading. Good job!' : 'One of the text boxes has ' + longPart + ' words and no subheading. Add subheadings so people can scan it.');
		}

		var passive = sentences.filter(function (sentence) { return PASSIVE.test(sentence); }).length;
		var passiveShare = percent(passive, sentences.length);
		add(passiveShare <= 10 ? 'good' : (passiveShare <= 15 ? 'ok' : 'bad'), 'Passive voice', passiveShare <= 10 ? 'Enough sentences say who does what. Great!' : passiveShare + '% of the sentences are in the passive voice ("is located", "are offered"); 10% is the most that reads lively. Say who does what.');

		var withTransition = sentences.filter(function (sentence) {
			var text = ' ' + plain(sentence) + ' ';
			return TRANSITIONS.some(function (word) { return text.indexOf(' ' + word + ' ') !== -1; });
		}).length;
		var transitionShare = percent(withTransition, sentences.length);
		add(transitionShare >= 30 ? 'good' : (transitionShare >= 20 ? 'ok' : 'bad'), 'Linking words', transitionShare >= 30 ? 'Well done! ' + transitionShare + '% of the sentences have a linking word.' : 'Only ' + transitionShare + '% of the sentences have a linking word such as "also", "because" or "however"; 30% makes a text flow.');

		// "The villas ..." and "The pool ..." begin differently: the word after "the" counts.
		var firstWord = function (sentence) {
			var list = wordsOf(sentence);
			return ['the', 'a', 'an'].indexOf(list[0]) !== -1 && list.length > 1 ? list[1] : list[0];
		};
		var run = 1;
		var longestRun = 1;
		for (var i = 1; i < sentences.length; i++) {
			run = firstWord(sentences[i]) === firstWord(sentences[i - 1]) ? run + 1 : 1;
			longestRun = Math.max(longestRun, run);
		}
		add(longestRun < 3 ? 'good' : 'bad', 'Sentence beginnings', longestRun < 3 ? 'The sentences begin in enough different ways. That is great!' : longestRun + ' sentences in a row begin with the same word. Vary them.');

		var totalSyllables = words.reduce(function (sum, word) { return sum + syllables(word); }, 0);
		var ease = Math.round(206.835 - 1.015 * (words.length / Math.max(1, sentences.length)) - 84.6 * (totalSyllables / words.length));
		ease = Math.max(0, Math.min(100, ease));
		add(ease >= 60 ? 'good' : (ease >= 40 ? 'ok' : 'bad'), 'Reading ease', 'The text scores ' + ease + ' out of 100 on the Flesch reading ease test' + (ease >= 60 ? ', which is easy to read. Good job!' : (ease >= 40 ? ', which is fairly difficult. Shorter sentences and simpler words help.' : ', which is difficult to read. Use shorter sentences and simpler words.')));
		return results;
	}

	function scoreOf(results) {
		if (!results.length) {
			return 0;
		}
		var points = results.reduce(function (sum, result) { return sum + { good: 9, ok: 6, bad: 3 }[result.state]; }, 0);
		return Math.round((points / (results.length * 9) - 1 / 3) * 150);
	}
	function levelOf(score) {
		return score > 70 ? 'good' : (score > 40 ? 'ok' : 'bad');
	}
	var LEVEL_WORDS = { good: 'Good', ok: 'OK', bad: 'Needs improvement' };

	// ---------------------------------------------------------------
	// Showing it
	// ---------------------------------------------------------------
	var tab = 'seo';
	var device = 'mobile';

	function cut(text, limit) {
		return text.length > limit ? text.slice(0, limit).replace(/\s+\S*$/, '') + ' …' : text;
	}
	// The words of the keyphrase are bold in a search result.
	function highlight(node, text, focus) {
		node.textContent = '';
		var wanted = focus === '' ? [] : keyWords(focus);
		text.split(/(\s+)/).forEach(function (piece) {
			if (wanted.indexOf(plain(piece)) !== -1) {
				var bold = document.createElement('b');
				bold.textContent = piece;
				node.appendChild(bold);
			} else {
				node.appendChild(document.createTextNode(piece));
			}
		});
	}

	function render() {
		var page = readPage();
		var seo = seoChecks(page);
		var readability = readabilityChecks(page);
		var scores = { seo: scoreOf(seo), readability: scoreOf(readability) };
		document.getElementById('dreSeoScore').value = scores.seo;
		document.getElementById('dreSeoReadability').value = scores.readability;

		// The two lights, here and wherever else the page shows them (the save bar)
		['seo', 'readability'].forEach(function (kind) {
			var level = levelOf(scores[kind]);
			document.querySelectorAll('[data-seo-light="' + kind + '"]').forEach(function (light) {
				light.className = light.className.replace(/\s*is-(good|ok|bad)/g, '') + ' is-' + level;
				var word = light.querySelector('em');
				if (word) { word.textContent = LEVEL_WORDS[level]; }
			});
			document.querySelectorAll('[data-seo-dot="' + kind + '"]').forEach(function (dot) { dot.className = 'is-' + level; });
		});

		// Google preview
		var base = (root.getAttribute('data-url') || '').replace(/^https?:\/\//, '').replace(/\/$/, '');
		root.querySelector('[data-seo-site]').textContent = root.getAttribute('data-site') || '';
		root.querySelector('[data-seo-url]').textContent = base.replace(/\//g, ' › ') + ' › ' + (page.slug || '…');
		root.querySelector('[data-seo-title]').textContent = cut(page.title || 'The title of the page', 60);
		highlight(root.querySelector('[data-seo-text]'), cut(page.description || 'Write a meta description. Without one, Google shows a piece of the page that it picks by itself.', device === 'mobile' ? 130 : 160), page.focus);
		root.querySelector('[data-seo-text]').classList.toggle('is-empty', page.description === '');

		// The results of the open tab: problems first, then improvements, then what is good
		var holder = document.getElementById('dreSeoResults');
		holder.textContent = '';
		var results = tab === 'seo' ? seo : readability;
		[['bad', 'Problems'], ['ok', 'Improvements'], ['good', 'Good results']].forEach(function (group) {
			var items = results.filter(function (result) { return result.state === group[0]; });
			if (!items.length) {
				return;
			}
			var heading = document.createElement('h4');
			heading.textContent = group[1] + ' (' + items.length + ')';
			holder.appendChild(heading);
			var list = document.createElement('ul');
			items.forEach(function (item) {
				var row = document.createElement('li');
				row.className = 'is-' + item.state;
				var dot = document.createElement('i');
				var text = document.createElement('span');
				var title = document.createElement('strong');
				title.textContent = item.title + ': ';
				text.appendChild(title);
				text.appendChild(document.createTextNode(item.text));
				row.appendChild(dot);
				row.appendChild(text);
				list.appendChild(row);
			});
			holder.appendChild(list);
		});
	}

	// ---------------------------------------------------------------
	// When to check again
	// ---------------------------------------------------------------
	var timer = null;
	function soon() {
		window.clearTimeout(timer);
		timer = window.setTimeout(render, 350);
	}
	form.addEventListener('input', soon);
	form.addEventListener('change', soon);
	// The text editors load after the page and report typing in their own way.
	var attached = [];
	window.setInterval(function () {
		if (!window.tinymce) {
			return;
		}
		window.tinymce.editors.forEach(function (editor) {
			if (attached.indexOf(editor.id) === -1) {
				attached.push(editor.id);
				editor.on('keyup change setcontent', soon);
				soon();
			}
		});
	}, 1000);

	root.addEventListener('click', function (event) {
		var tabButton = event.target.closest('[data-seo-tab]');
		var deviceButton = event.target.closest('[data-seo-device]');
		if (tabButton) {
			tab = tabButton.getAttribute('data-seo-tab');
			root.querySelectorAll('[data-seo-tab]').forEach(function (button) { button.classList.toggle('is-on', button === tabButton); });
			render();
		}
		if (deviceButton) {
			device = deviceButton.getAttribute('data-seo-device');
			root.querySelectorAll('[data-seo-device]').forEach(function (button) { button.classList.toggle('is-on', button === deviceButton); });
			document.getElementById('dreSeoSnippet').className = 'dre-seo-snippet is-' + device;
			render();
		}
	});

	document.querySelectorAll('[data-seo-light]').forEach(function (light) {
		light.addEventListener('click', function () {
			var targetTab = light.getAttribute('data-seo-light');
			if (targetTab && (targetTab === 'seo' || targetTab === 'readability')) {
				tab = targetTab;
				root.querySelectorAll('[data-seo-tab]').forEach(function (button) {
					button.classList.toggle('is-on', button.getAttribute('data-seo-tab') === tab);
				});
				render();
			}
		});
	});

	// The text editors copy their content to the form only on saving; the scores are worked out just before.
	form.addEventListener('submit', render, true);
	render();
})();
