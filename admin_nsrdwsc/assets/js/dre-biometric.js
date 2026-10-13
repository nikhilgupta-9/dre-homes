// Fingerprint and face sign-in (WebAuthn): the browser side, used by the sign-in page and the profile page.
// The device decides how it checks the person: Touch ID on a Mac, Face ID or Touch ID on an iPhone or iPad,
// face, fingerprint or PIN with Windows Hello, fingerprint or face unlock on Android.
//
//   DreBiometric.available()            -> Promise<boolean>  this device has a fingerprint reader or face check the browser can use
//   DreBiometric.method()               -> {name, icon}     what that check is called on this device, for buttons
//   DreBiometric.signIn()               -> Promise<answer>   sign-in page
//   DreBiometric.setUp(csrf, name)      -> Promise<answer>   profile page: set this device up
//   DreBiometric.remove(csrf, id)       -> Promise<answer>   profile page: forget a device
//
// Every promise gives the server's answer {status: 'success' | 'error', msg: '...'} and never rejects.
window.DreBiometric = (function () {
	var url = base_url + 'ajax/biometric.php';

	function toBytes(text) {
		var plain = atob(text.replace(/-/g, '+').replace(/_/g, '/'));
		var bytes = new Uint8Array(plain.length);
		for (var i = 0; i < plain.length; i++) {
			bytes[i] = plain.charCodeAt(i);
		}
		return bytes;
	}

	function toText(buffer) {
		var bytes = new Uint8Array(buffer);
		var plain = '';
		for (var i = 0; i < bytes.length; i++) {
			plain += String.fromCharCode(bytes[i]);
		}
		return btoa(plain).replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '');
	}

	function ask(fields) {
		var body = new FormData();
		Object.keys(fields).forEach(function (name) {
			body.append(name, fields[name]);
		});
		return fetch(url, { method: 'POST', body: body, credentials: 'same-origin' })
			.then(function (response) { return response.json(); })
			.catch(function () { return { status: 'error', msg: 'The server could not be reached. Please try again.' }; });
	}

	// What went wrong on the device, in plain words.
	function deviceProblem(error, signingIn) {
		var name = error && error.name;
		if (name === 'NotAllowedError') {
			// The browser gives the same answer for "cancelled" and for "this device has nothing set up for this website".
			return signingIn
				? 'Nothing was confirmed. If this device is not set up yet: sign in with your password once, open My Profile and set it up there. After that this button works.'
				: 'The check was cancelled or took too long. Press the button again and confirm on the device.';
		}
		if (name === 'InvalidStateError') {
			return 'This device is already set up.';
		}
		if (name === 'SecurityError') {
			return 'Fingerprint and face sign-in only work on the secure (https) address of the admin panel.';
		}
		return 'This device could not do the fingerprint or face check.';
	}

	function available() {
		if (!window.PublicKeyCredential || !window.isSecureContext || !PublicKeyCredential.isUserVerifyingPlatformAuthenticatorAvailable) {
			return Promise.resolve(false);
		}
		return PublicKeyCredential.isUserVerifyingPlatformAuthenticatorAvailable().catch(function () { return false; });
	}

	// What the device's own check is called, so a button can say "Sign in with Face ID" instead of something general.
	function method() {
		var agent = navigator.userAgent;
		var touchMac = /Macintosh/.test(agent) && navigator.maxTouchPoints > 1;
		if (/iPhone|iPad|iPod/.test(agent) || touchMac) {
			return { name: 'Face ID or Touch ID', icon: 'bi-person-bounding-box' };
		}
		if (/Macintosh/.test(agent)) {
			return { name: 'Touch ID', icon: 'bi-fingerprint' };
		}
		if (/Windows/.test(agent)) {
			return { name: 'Windows Hello (face or fingerprint)', icon: 'bi-person-bounding-box' };
		}
		if (/Android/.test(agent)) {
			return { name: 'fingerprint or face unlock', icon: 'bi-fingerprint' };
		}
		return { name: 'fingerprint or face', icon: 'bi-fingerprint' };
	}

	function signIn() {
		return ask({ action: 'login_options' }).then(function (start) {
			if (start.status !== 'success') {
				return start;
			}
			return navigator.credentials.get({ publicKey: {
				challenge: toBytes(start.options.challenge),
				rpId: start.options.rpId,
				userVerification: 'required',
				timeout: 60000
			} }).then(function (credential) {
				return ask({ action: 'login_verify', data: JSON.stringify({
					id: credential.id,
					clientDataJSON: toText(credential.response.clientDataJSON),
					authenticatorData: toText(credential.response.authenticatorData),
					signature: toText(credential.response.signature)
				}) });
			}, function (error) {
				return { status: 'error', msg: deviceProblem(error, true) };
			});
		});
	}

	function setUp(csrf, deviceName) {
		return ask({ action: 'register_options', csrf: csrf }).then(function (start) {
			if (start.status !== 'success') {
				return start;
			}
			var options = start.options;
			return navigator.credentials.create({ publicKey: {
				challenge: toBytes(options.challenge),
				rp: options.rp,
				user: { id: toBytes(options.user.id), name: options.user.name, displayName: options.user.displayName },
				// ES256 first (Touch ID and most devices), then RS256 (some Windows Hello devices).
				pubKeyCredParams: [{ type: 'public-key', alg: -7 }, { type: 'public-key', alg: -257 }],
				// The laptop's or phone's own reader, with the key kept on the device so no email has to be typed.
				authenticatorSelection: { authenticatorAttachment: 'platform', residentKey: 'required', requireResidentKey: true, userVerification: 'required' },
				excludeCredentials: options.exclude.map(function (id) { return { type: 'public-key', id: toBytes(id) }; }),
				attestation: 'none',
				timeout: 60000
			} }).then(function (credential) {
				return ask({ action: 'register_verify', csrf: csrf, device_name: deviceName || '', data: JSON.stringify({
					clientDataJSON: toText(credential.response.clientDataJSON),
					attestationObject: toText(credential.response.attestationObject)
				}) });
			}, function (error) {
				return { status: 'error', msg: deviceProblem(error) };
			});
		});
	}

	function remove(csrf, passkeyId) {
		return ask({ action: 'remove', csrf: csrf, passkey_id: passkeyId });
	}

	return { available: available, method: method, signIn: signIn, setUp: setUp, remove: remove };
})();
