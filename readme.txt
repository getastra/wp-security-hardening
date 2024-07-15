===WP Hardening (discontinued) ===
Contributors: astrasecuritysuite
Donate link: https://www.webprotect.ai
Tags:  discontinued
Requires at least: 4.3
Tested up to: 6.0.3
Stable tag: 1.2.6
Requires PHP: 5.3
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

The WP Hardening is a one-click tool to fix standard security recommendations on your WordPress website.

== IMPORTANT: This plugin is discontinued ==

<strong>This is to inform you that this plugin is no longer being maintained or updated. We have placed a discontinuation request with the WordPress team, and the plugin will soon be 'closed' for new installations.

This plugin was launched as a side project and has sadly reached the end of its journey. Thank you for your understanding and for using our plugin. We apologize for any inconvenience this may cause.

<strong>What This Means for You</strong>
<ol>
<li><strong>No Further Updates:</strong> There will be no more updates, bug fixes, or new features.</li>
<li><strong>No Support:</strong> Support for this plugin is no longer available.</li>
</ol>

We recommend that you deactivate and delete this plugin from your WordPress site as soon as possible. Please seek alternative plugins to replace the functionality provided by this plugin.</strong>

== Description ==

WP Hardening is a tool which performs a real-time security audit of your website to find missing security best practices. Using our ‘Security Fixer’ you can also fix these with a single click from your WordPress backend.

<br/>
==Features==

###Hardening Audit###
<ol>
  <li><strong>WordPress Version Check</strong>
    It checks if your website is on the latest version or not.<br>
  </li>
  <li><strong>Checking Outdated Plugins</strong>
    It checks if your website is running the updated plugins or not.<br>
  </li>
  <li><strong>Checking PHP Version</strong>
    WP Hardening also checks if your website is running on a secure version of PHP.<br>
  </li>
  <li><strong>Checking File & Folder Permissions</strong>
    WP Hardening also checks if your website is built on the secured version of PHP or not.<br>
  </li>
  <li><strong>Database Password Strength</strong>
    We check the strength of passwords used on your database. Not having a secured password can become an easy target for Brute-Force attacks.<br>
  </li>
  <li><strong>Checking Firewall Protection</strong>
    We’ll check if your website is being protected by a firewall or not. Firewalls leverage a great monitoring and filtering system on your website.<br>
  </li>
</ol>

##Security Fixers##

**Admin & API Security**

<ol>
<li><strong>Stop User Enumeration</strong> Hackers & bad bots can easily find usernames in WordPress by visiting URLs like <em>yourwebsite.com/?author=1</em>. This can significantly help them in performing larger attacks like Bruteforce & SQL injection.</li>
<li><strong>Change Login URL</strong> Prevent admin password brute-forcing by changing the URL for the wp-admin login area. You can change the url only when this fixer is disabled.</li>
<li><strong>Disable XMLRPC</strong> XMLRPC is often targeted by bots to perform brute force & DDoS attacks (via pingback) causing considerable stress on your server. However, there are some services which rely on xmlrpc. Be sure you definitely do not need xmlrpc before disabling it.</li>
<li><strong>Disable WP API JSON</strong> Since 4.4 version, WordPress added JSON REST API which largely benefits developers. However, it’s often targeted for bruteforce attacks just like in the case of xmlrpc. If you are not using it, best is to disable it.</li>
<li><strong>Disable File Editor</strong> If a hacker is able to get access to your WordPress admin, with the file editor enabled it becomes quite easy for them to add malicious code to your theme or plugins. If you are not using this, it’s best to keep the file editor disabled.</li>
<li><strong>Disable WordPress Application Passwords</strong> WordPress application passwords have full permissions of the user that generated them, making it possible for an attacker to gain control of a website by tricking the site administrator into granting permission to their malicious application.</li>
</ol>


**Disable Information Disclosure & Remove Meta information**

<ol>
  <li><strong>Hide WordPress version number</strong>
    This gives away your WordPress version number making life of a hacker simple as they’ll be able to find targeted exploits for your WordPress version. It’s best to keep this hidden, enabling the button shall do that.<br>
  </li>
  <li><strong>Remove WordPress Meta Generator Tag</strong>
    The WordPress Meta tag contains your WordPress version number which is best kept hidden<br>
  </li>
  <li><strong>Remove WPML (WordPress Multilingual Plugin) Meta Generator Tag</strong>
    This discloses the WordPress version number which is best kept hidden.<br>
  </li>
  <li><strong>Remove Slider Revolution Meta Generator Tag</strong>
    Slider revolution stays on the radar of hackers due to its popularity. An overnight hack in the version you’re using could lead your website vulnerable too. Make it difficult for hackers to exploit the vulnerabilities by disabling version number disclosure here<br>
  </li>
  <li><strong>Remove WPBakery Page Builder Meta Generator Tag</strong>
    Common page builders often are diagnosed with a vulnerability putting your website’s security at risk. With this toggle enabled, the version of these page builders will be hidden making it difficult for hackers to find if you’re using a vulnerable version.<br>
  </li>
  <li><strong>Remove Version from Stylesheet</strong>
    Many CSS files have the WordPress version number appended to their source, for cache purposes. Knowing the version number allows hackers to exploit known vulnerabilities.<br>
  </li>
  <li><strong>Remove Version from Script</strong>
    Many JS files have the WordPress version number appended to their source, for cache purposes. Knowing the version number allows hackers to exploit known vulnerabilities.<br>
  </li>
</ol>

**Basic Server Hardening**
<ol>
	<li><strong>Hide Directory Listing of WP includes</strong>
WP-includes directory gives away a lot of information about your WordPress to hackers. Disable it by simply toggling the option to ensure you make reconnaissance of hackers difficult</li>
</ol>
<br/>

**Security Headers**
<ol>
	<li><strong>Clickjacking Protection</strong>
Protect your WordPress Website from clickjacking with the X-Frame-Options response header. Clickjacking is an attack that tricks a user into clicking a webpage element which is invisible or disguised as another element.</li>
	<li><strong>XSS Protection</strong>
Add the HTTP X-XSS-Protection response header so that browsers such as Chrome, Safari, Microsoft Edge stops pages from loading when they detect reflected cross-site scripting (XSS) attacks.</li>
	<li><strong>Content Sniffing protection</strong>
Add the X-Content-Type-Options response header to protect against MIME sniffing vulnerabilities. Such vulnerabilities can occur when a website allows users to upload content to a website, however the user disguises a particular file type as something else. This can give them the opportunity to perform cross-site scripting and compromise the website.
</li>
	<li><strong>HTTP only & Secure flag</strong>
Enable the HttpOnly and secure flags to make the cookies more secure. This instructs the browser to trust the cookie only by the server, which adds a layer of protection against XSS attacks.</li>
</ol>

<br/>

== Installation ==

<ol>
  <li>Visit ‘Plugins > Add New’ in your admin dashboard</li>
  <li>Search for ‘WP-Hardening’</li>
  <li>Install WP-Hardening once it appears</li>
  <li>Activate it from your Plugins page</li>
  <li>WP-Hardening button will appear on the bottom left of your admin dashboard</li>
</ol>

<br/>

==Frequently Asked Questions==

=Is WP hardening plugin free to use?=

Yes, it is absolutely free. Just download the plugin and activate it from your backend. Run the scan and review the results.

=How does WP Hardening plugin works?=

WP Hardening scans your website for security recommendations like File Permissions, WordPress Version, Outdated plugins etc. & helps you with proper steps to fix these issues. The ‘Security Fixer’ button help to fix Admin & API security, Disable Information Disclosure & Remove Meta information & Basic Server Hardening.

=Will this plugin help me with malware infected website?=

No, this plugin will help you harden your WordPress Security.

=How will I get informed about my website’s security?=

You will get informed instantly after each scan via email. For additional information, subscribe to our newsletter and stay updated.

=Does WP Hardening conflict with other security plugins?=
No, WP Hardening does not conflict with any security plugin. However, you can get rid of multiple plugins that you have installed to disable XMLRPC, prevent user enumeration, changing admin URL, etc. In case, you face any issues with the WP hardening plugin, feel free to send us a mail.

<br/>
== Screenshots ==

Harden security with WP Hardening

1. This is the main dashboard; you’ll find a concise overview of your website’s present security. Buttons “Start a new audit”, “Security Fixers”, “Request malware cleanup”, “View Help docs”, on the dashboard take you to the respective sections.
2. 'Audit Recommendation' section on the same page details the audit results. Whereas the “Recommendations” sub-section show improvement areas with links to comprehensive guide to implement those practices.
3. 'Passed test' sub-section shows already implemented best practices.
4. The 'Security Fixers' section contains 13 vital security hardening areas. You can optimize these with a single click.
5. The first section in the security fixer is of 'Admin & API Security'. You can find the details of each test by hovering.
6. The second & third section are 'Disable Information Disclosure & Remove Meta information' & 'Basic Server Hardening'.

== Changelog ==

= 1.2 - January 31, 2020 =
	* Improvement: Add security headers to the HTTP response
	* Improvement: Changing the frequency of Hardening audits
	* Improvement: Configure emails to be sent to upto 15 people
    * Fix: jQuery bug on fixers page
= 1.1 - March 31, 2020 =
    * Initial public release of WP Hardening Plugin.
