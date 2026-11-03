-- Make both legal pages editable from Admin > Legal Pages.
ALTER TABLE tbl_privacy_policy
  ADD COLUMN terms_heading VARCHAR(255) NOT NULL DEFAULT 'Terms & Conditions' AFTER privacy_policy_id,
  ADD COLUMN terms_descp LONGTEXT NULL AFTER terms_heading,
  ADD COLUMN privacy_policy_content_heading VARCHAR(255) NOT NULL DEFAULT '' AFTER privacy_policy_heading;

UPDATE tbl_privacy_policy
SET terms_heading = IF(TRIM(terms_heading) = '', 'Terms & Conditions', terms_heading),
    terms_descp = IF(terms_descp IS NULL OR TRIM(terms_descp) = '', '<p>Please be advised that accessing or using this website implies the user''s consent to abide by the following terms and conditions:</p>
<p><b>Intellectual Property</b></p>
<p>All content, images, logos, property listings, and other materials on this website are the property of DRE Homes Real Estate Broker LLC, respective project developers, or their licensors. These materials are protected under applicable intellectual property laws, and users are strictly prohibited from copying, altering, distributing, or utilizing any content without obtaining prior written permission.</p>
<p><b>Property Information</b></p>
<p>The content of this website, including property details, prices, payment plans, and availability, is subject to change at any time without prior notice. The Company shall not be held responsible for any inaccuracies, omissions, or decisions made based on the information provided.</p>
<p><b>User Obligations</b></p>
<p>Users are required to provide accurate and current information when submitting inquiries or requests via the website. Users must refrain from engaging in unlawful or harmful activities, including hacking, spamming, unauthorized access, or transmitting malicious software.</p>
<p><b>Privacy and Data Collection</b></p>
<p>The Company may collect, store, and use your personal information in accordance with its Privacy Policy. We may also use third-party services or tracking technologies such as cookies and analytics tools to understand usage behavior and improve our services.</p>
<p><b>Disclaimer of Liability</b></p>
<p>The Company disclaims liability for any damages, losses, or claims arising from the use of the website or reliance on the provided information. This includes, but is not limited to, financial losses, investment decisions, property-related matters, or any direct or indirect damages.</p>
<p><b>Links to Third-Party Websites</b></p>
<p>This website may contain links to external websites or resources. We do not endorse or assume responsibility for the content, privacy practices, or availability of such third-party websites.</p>
<p><b>Modifications to the Terms</b></p>
<p>The Company may update or modify these Terms and Conditions at any time without prior notice. Continued use of this website constitutes acceptance of the revised terms.</p>
<p><b>Governing Law &amp; Dispute Resolution</b></p>
<p><b>Governing Law</b></p>
<p>Your access to and use of this website, together with these Terms, shall be subject to and construed in accordance with the applicable laws of the Emirate of Dubai and the federal laws of the United Arab Emirates. This applies to any dispute or claim regardless of whether it arises under contract or otherwise.</p>
<p><b>Jurisdiction</b></p>
<p>All disputes relating to the website or these Terms shall be submitted to the competent courts of Dubai, United Arab Emirates, which shall have primary jurisdiction. The Company also reserves the right to commence legal proceedings in any other jurisdiction where a user resides or where a violation of these Terms has occurred. By using this website, you agree to submit to such jurisdiction and waive any objection to the venue or legal authority of those courts.</p>', terms_descp),
    privacy_policy_content_heading = IF(TRIM(privacy_policy_content_heading) = '', 'PRIVACY POLICY & TERM OF USES', privacy_policy_content_heading);