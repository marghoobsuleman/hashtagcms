<?php
namespace MarghoobSuleman\HashtagCms\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PageTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $table_name = 'pages';
        $table_name_langs = 'page_langs';
        $date = date('Y-m-d H:i:s');


        $pages = array(
            array('id' => '1','site_id' => '1','microsite_id' => '0','tenant_id' => '1','category_id' => '9','alias' => 'TEST','exclude_in_listing' => '0','content_type' => 'page','position' => '3','link_rewrite' => 'tnc','link_navigation' => NULL,'menu_placement' => 'bottom','header_content' => NULL,'footer_content' => NULL,'insert_by' => '1','update_by' => '1','required_login' => '0','publish_status' => '1','created_at' => NULL,'enable_comments' => '0','updated_at' => '2020-07-02 13:37:37','deleted_at' => NULL),
            array('id' => '2','site_id' => '1','microsite_id' => '0','tenant_id' => NULL,'category_id' => '9','alias' => 'PRIVACY_POLICY','exclude_in_listing' => '0','content_type' => 'page','position' => '4','link_rewrite' => 'privacy-policy','link_navigation' => NULL,'menu_placement' => 'bottom','header_content' => NULL,'footer_content' => NULL,'insert_by' => '1','update_by' => '1','required_login' => '0','publish_status' => '1','created_at' => NULL,'enable_comments' => '0','updated_at' => '2020-07-05 09:12:13','deleted_at' => NULL),
            array('id' => '3','site_id' => '1','microsite_id' => '0','tenant_id' => NULL,'category_id' => '2','alias' => 'PAUSE_A_LOOP','exclude_in_listing' => '0','content_type' => 'blog','position' => '4','link_rewrite' => 'pause-a-loop','link_navigation' => NULL,'menu_placement' => NULL,'header_content' => NULL,'footer_content' => NULL,'insert_by' => '1','update_by' => '1','required_login' => '0','publish_status' => '1','created_at' => '2020-07-11 10:50:06','enable_comments' => '1','updated_at' => '2020-07-11 10:50:06','deleted_at' => NULL),
            array('id' => '4','site_id' => '1','microsite_id' => '0','tenant_id' => NULL,'category_id' => '2','alias' => 'TEST_BLOG','exclude_in_listing' => '0','content_type' => 'blog','position' => '4','link_rewrite' => 'test-blog','link_navigation' => NULL,'menu_placement' => NULL,'header_content' => NULL,'footer_content' => NULL,'insert_by' => '1','update_by' => '1','required_login' => '0','publish_status' => '1','created_at' => '2020-07-11 05:50:06','enable_comments' => '1','updated_at' => NULL,'deleted_at' => NULL)
        );


        $page_langs = array(
            array('page_id' => '1','lang_id' => '1','name' => 'Terms and Conditions','title' => 'Terms and Conditions','description' => NULL,'page_content' => '<h2 style="box-sizing: border-box; font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif; line-height: 1.1; color: #333333; margin: 0px 0px 1em; font-size: 20px; background-color: #ffffff;"><span style="box-sizing: border-box;">Terms and Conditions @php echo date(\'Y-m-d H:i:s\'); @endphp</span></h2>
<p style="box-sizing: border-box; margin: 1em 0px; color: #333333; font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 16px; background-color: #ffffff;">Welcome to&nbsp;<span class="highlight preview_website_name" style="box-sizing: border-box; background: #ffffee;">Website Name</span>!</p>
<p style="box-sizing: border-box; margin: 1em 0px; color: #333333; font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 16px; background-color: #ffffff;">These terms and conditions outline the rules and regulations for the use of&nbsp;<span class="highlight preview_company_name" style="box-sizing: border-box; background: #ffffee;">Company Name</span>\'s Website, located at&nbsp;<span class="highlight preview_website_url" style="box-sizing: border-box; background: #ffffee;">Website.com</span>.</p>
<p style="box-sizing: border-box; margin: 1em 0px; color: #333333; font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 16px; background-color: #ffffff;">By accessing this website we assume you accept these terms and conditions. Do not continue to use&nbsp;<span class="highlight preview_website_name" style="box-sizing: border-box; background: #ffffee;">Website Name</span>&nbsp;if you do not agree to take all of the terms and conditions stated on this page.</p>
<p style="box-sizing: border-box; margin: 1em 0px; color: #333333; font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 16px; background-color: #ffffff;">The following terminology applies to these Terms and Conditions, Privacy Statement and Disclaimer Notice and all Agreements: &ldquo;Client&rdquo;, &ldquo;You&rdquo; and &ldquo;Your&rdquo; refers to you, the person log on this website and compliant to the Company\'s terms and conditions. &ldquo;The Company&rdquo;, &ldquo;Ourselves&rdquo;, &ldquo;We&rdquo;, &ldquo;Our&rdquo; and &ldquo;Us&rdquo;, refers to our Company. &ldquo;Party&rdquo;, &ldquo;Parties&rdquo;, or &ldquo;Us&rdquo;, refers to both the Client and ourselves. All terms refer to the offer, acceptance and consideration of payment necessary to undertake the process of our assistance to the Client in the most appropriate manner for the express purpose of meeting the Client\'s needs in respect of provision of the Company\'s stated services, in accordance with and subject to, prevailing law of Netherlands. Any use of the above terminology or other words in the singular, plural, capitalization and/or he/she or they, are taken as interchangeable and therefore as referring to same.</p>
<h3 style="box-sizing: border-box; font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif; line-height: 1.1; color: #333333; margin: 0px 0px 1em; font-size: 16px; background-color: #ffffff;"><span style="box-sizing: border-box;">Cookies</span></h3>
<p style="box-sizing: border-box; margin: 1em 0px; color: #333333; font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 16px; background-color: #ffffff;">We employ the use of cookies. By accessing&nbsp;<span class="highlight preview_website_name" style="box-sizing: border-box; background: #ffffee;">Website Name</span>, you agreed to use cookies in agreement with the&nbsp;<span class="highlight preview_company_name" style="box-sizing: border-box; background: #ffffee;">Company Name</span>\'s Privacy Policy.</p>
<p style="box-sizing: border-box; margin: 1em 0px; color: #333333; font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 16px; background-color: #ffffff;">Most interactive websites use cookies to let us retrieve the user\'s details for each visit. Cookies are used by our website to enable the functionality of certain areas to make it easier for people visiting our website. Some of our affiliate/advertising partners may also use cookies.</p>
<h3 style="box-sizing: border-box; font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif; line-height: 1.1; color: #333333; margin: 0px 0px 1em; font-size: 16px; background-color: #ffffff;"><span style="box-sizing: border-box;">License</span></h3>
<p style="box-sizing: border-box; margin: 1em 0px; color: #333333; font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 16px; background-color: #ffffff;">Unless otherwise stated,&nbsp;<span class="highlight preview_company_name" style="box-sizing: border-box; background: #ffffee;">Company Name</span>&nbsp;and/or its licensors own the intellectual property rights for all material on&nbsp;<span class="highlight preview_website_name" style="box-sizing: border-box; background: #ffffee;">Website Name</span>. All intellectual property rights are reserved. You may access this from&nbsp;<span class="highlight preview_website_name" style="box-sizing: border-box; background: #ffffee;">Website Name</span>&nbsp;for your own personal use subjected to restrictions set in these terms and conditions.</p>
<p style="box-sizing: border-box; margin: 1em 0px; color: #333333; font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 16px; background-color: #ffffff;">You must not:</p>
<ul style="box-sizing: border-box; margin: 1em 0px; padding: 0px 0px 0px 1em; list-style-position: outside; color: #333333; font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 16px; background-color: #ffffff;">
<li style="box-sizing: border-box; margin-top: 0.5em; margin-bottom: 0.5em;">Republish material from&nbsp;<span class="highlight preview_website_name" style="box-sizing: border-box; background: #ffffee;">Website Name</span></li>
<li style="box-sizing: border-box; margin-top: 0.5em; margin-bottom: 0.5em;">Sell, rent or sub-license material from&nbsp;<span class="highlight preview_website_name" style="box-sizing: border-box; background: #ffffee;">Website Name</span></li>
<li style="box-sizing: border-box; margin-top: 0.5em; margin-bottom: 0.5em;">Reproduce, duplicate or copy material from&nbsp;<span class="highlight preview_website_name" style="box-sizing: border-box; background: #ffffee;">Website Name</span></li>
<li style="box-sizing: border-box; margin-top: 0.5em; margin-bottom: 0.5em;">Redistribute content from&nbsp;<span class="highlight preview_website_name" style="box-sizing: border-box; background: #ffffee;">Website Name</span></li>
</ul>
<p style="box-sizing: border-box; margin: 1em 0px; color: #333333; font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 16px; background-color: #ffffff;">This Agreement shall begin on the date hereof.</p>
<p style="box-sizing: border-box; margin: 1em 0px; color: #333333; font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 16px; background-color: #ffffff;">Parts of this website offer an opportunity for users to post and exchange opinions and information in certain areas of the website.&nbsp;<span class="highlight preview_company_name" style="box-sizing: border-box; background: #ffffee;">Company Name</span>&nbsp;does not filter, edit, publish or review Comments prior to their presence on the website. Comments do not reflect the views and opinions of&nbsp;<span class="highlight preview_company_name" style="box-sizing: border-box; background: #ffffee;">Company Name</span>,its agents and/or affiliates. Comments reflect the views and opinions of the person who post their views and opinions. To the extent permitted by applicable laws,&nbsp;<span class="highlight preview_company_name" style="box-sizing: border-box; background: #ffffee;">Company Name</span>&nbsp;shall not be liable for the Comments or for any liability, damages or expenses caused and/or suffered as a result of any use of and/or posting of and/or appearance of the Comments on this website.</p>
<p style="box-sizing: border-box; margin: 1em 0px; color: #333333; font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 16px; background-color: #ffffff;"><span class="highlight preview_company_name" style="box-sizing: border-box; background: #ffffee;">Company Name</span>&nbsp;reserves the right to monitor all Comments and to remove any Comments which can be considered inappropriate, offensive or causes breach of these Terms and Conditions.</p>
<p style="box-sizing: border-box; margin: 1em 0px; color: #333333; font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 16px; background-color: #ffffff;">You warrant and represent that:</p>
<ul style="box-sizing: border-box; margin: 1em 0px; padding: 0px 0px 0px 1em; list-style-position: outside; color: #333333; font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 16px; background-color: #ffffff;">
<li style="box-sizing: border-box; margin-top: 0.5em; margin-bottom: 0.5em;">You are entitled to post the Comments on our website and have all necessary licenses and consents to do so;</li>
<li style="box-sizing: border-box; margin-top: 0.5em; margin-bottom: 0.5em;">The Comments do not invade any intellectual property right, including without limitation copyright, patent or trademark of any third party;</li>
<li style="box-sizing: border-box; margin-top: 0.5em; margin-bottom: 0.5em;">The Comments do not contain any defamatory, libelous, offensive, indecent or otherwise unlawful material which is an invasion of privacy</li>
<li style="box-sizing: border-box; margin-top: 0.5em; margin-bottom: 0.5em;">The Comments will not be used to solicit or promote business or custom or present commercial activities or unlawful activity.</li>
</ul>
<p style="box-sizing: border-box; margin: 1em 0px; color: #333333; font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 16px; background-color: #ffffff;">You hereby grant&nbsp;<span class="highlight preview_company_name" style="box-sizing: border-box; background: #ffffee;">Company Name</span>&nbsp;a non-exclusive license to use, reproduce, edit and authorize others to use, reproduce and edit any of your Comments in any and all forms, formats or media.</p>
<h3 style="box-sizing: border-box; font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif; line-height: 1.1; color: #333333; margin: 0px 0px 1em; font-size: 16px; background-color: #ffffff;"><span style="box-sizing: border-box;">Hyperlinking to our Content</span></h3>
<p style="box-sizing: border-box; margin: 1em 0px; color: #333333; font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 16px; background-color: #ffffff;">The following organizations may link to our Website without prior written approval:</p>
<ul style="box-sizing: border-box; margin: 1em 0px; padding: 0px 0px 0px 1em; list-style-position: outside; color: #333333; font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 16px; background-color: #ffffff;">
<li style="box-sizing: border-box; margin-top: 0.5em; margin-bottom: 0.5em;">Government agencies;</li>
<li style="box-sizing: border-box; margin-top: 0.5em; margin-bottom: 0.5em;">Search engines;</li>
<li style="box-sizing: border-box; margin-top: 0.5em; margin-bottom: 0.5em;">News organizations;</li>
<li style="box-sizing: border-box; margin-top: 0.5em; margin-bottom: 0.5em;">Online directory distributors may link to our Website in the same manner as they hyperlink to the Websites of other listed businesses; and</li>
<li style="box-sizing: border-box; margin-top: 0.5em; margin-bottom: 0.5em;">System wide Accredited Businesses except soliciting non-profit organizations, charity shopping malls, and charity fundraising groups which may not hyperlink to our Web site.</li>
</ul>
<p style="box-sizing: border-box; margin: 1em 0px; color: #333333; font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 16px; background-color: #ffffff;">These organizations may link to our home page, to publications or to other Website information so long as the link: (a) is not in any way deceptive; (b) does not falsely imply sponsorship, endorsement or approval of the linking party and its products and/or services; and (c) fits within the context of the linking party\'s site.</p>
<p style="box-sizing: border-box; margin: 1em 0px; color: #333333; font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 16px; background-color: #ffffff;">We may consider and approve other link requests from the following types of organizations:</p>
<ul style="box-sizing: border-box; margin: 1em 0px; padding: 0px 0px 0px 1em; list-style-position: outside; color: #333333; font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 16px; background-color: #ffffff;">
<li style="box-sizing: border-box; margin-top: 0.5em; margin-bottom: 0.5em;">commonly-known consumer and/or business information sources;</li>
<li style="box-sizing: border-box; margin-top: 0.5em; margin-bottom: 0.5em;">dot.com community sites;</li>
<li style="box-sizing: border-box; margin-top: 0.5em; margin-bottom: 0.5em;">associations or other groups representing charities;</li>
<li style="box-sizing: border-box; margin-top: 0.5em; margin-bottom: 0.5em;">online directory distributors;</li>
<li style="box-sizing: border-box; margin-top: 0.5em; margin-bottom: 0.5em;">internet portals;</li>
<li style="box-sizing: border-box; margin-top: 0.5em; margin-bottom: 0.5em;">accounting, law and consulting firms; and</li>
<li style="box-sizing: border-box; margin-top: 0.5em; margin-bottom: 0.5em;">educational institutions and trade associations.</li>
</ul>
<p style="box-sizing: border-box; margin: 1em 0px; color: #333333; font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 16px; background-color: #ffffff;">We will approve link requests from these organizations if we decide that: (a) the link would not make us look unfavorably to ourselves or to our accredited businesses; (b) the organization does not have any negative records with us; (c) the benefit to us from the visibility of the hyperlink compensates the absence of&nbsp;<span class="highlight preview_company_name" style="box-sizing: border-box; background: #ffffee;">Company Name</span>; and (d) the link is in the context of general resource information.</p>
<p style="box-sizing: border-box; margin: 1em 0px; color: #333333; font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 16px; background-color: #ffffff;">These organizations may link to our home page so long as the link: (a) is not in any way deceptive; (b) does not falsely imply sponsorship, endorsement or approval of the linking party and its products or services; and (c) fits within the context of the linking party\'s site.</p>
<p style="box-sizing: border-box; margin: 1em 0px; color: #333333; font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 16px; background-color: #ffffff;">If you are one of the organizations listed in paragraph 2 above and are interested in linking to our website, you must inform us by sending an e-mail to&nbsp;<span class="highlight preview_company_name" style="box-sizing: border-box; background: #ffffee;">Company Name</span>. Please include your name, your organization name, contact information as well as the URL of your site, a list of any URLs from which you intend to link to our Website, and a list of the URLs on our site to which you would like to link. Wait 2-3 weeks for a response.</p>
<p style="box-sizing: border-box; margin: 1em 0px; color: #333333; font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 16px; background-color: #ffffff;">Approved organizations may hyperlink to our Website as follows:</p>
<ul style="box-sizing: border-box; margin: 1em 0px; padding: 0px 0px 0px 1em; list-style-position: outside; color: #333333; font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 16px; background-color: #ffffff;">
<li style="box-sizing: border-box; margin-top: 0.5em; margin-bottom: 0.5em;">By use of our corporate name; or</li>
<li style="box-sizing: border-box; margin-top: 0.5em; margin-bottom: 0.5em;">By use of the uniform resource locator being linked to; or</li>
<li style="box-sizing: border-box; margin-top: 0.5em; margin-bottom: 0.5em;">By use of any other description of our Website being linked to that makes sense within the context and format of content on the linking party\'s site.</li>
</ul>
<p style="box-sizing: border-box; margin: 1em 0px; color: #333333; font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 16px; background-color: #ffffff;">No use of&nbsp;<span class="highlight preview_company_name" style="box-sizing: border-box; background: #ffffee;">Company Name</span>\'s logo or other artwork will be allowed for linking absent a trademark license agreement.</p>
<h3 style="box-sizing: border-box; font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif; line-height: 1.1; color: #333333; margin: 0px 0px 1em; font-size: 16px; background-color: #ffffff;"><span style="box-sizing: border-box;">iFrames</span></h3>
<p style="box-sizing: border-box; margin: 1em 0px; color: #333333; font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 16px; background-color: #ffffff;">Without prior approval and written permission, you may not create frames around our Webpages that alter in any way the visual presentation or appearance of our Website.</p>
<h3 style="box-sizing: border-box; font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif; line-height: 1.1; color: #333333; margin: 0px 0px 1em; font-size: 16px; background-color: #ffffff;"><span style="box-sizing: border-box;">Content Liability</span></h3>
<p style="box-sizing: border-box; margin: 1em 0px; color: #333333; font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 16px; background-color: #ffffff;">We shall not be hold responsible for any content that appears on your Website. You agree to protect and defend us against all claims that is rising on your Website. No link(s) should appear on any Website that may be interpreted as libelous, obscene or criminal, or which infringes, otherwise violates, or advocates the infringement or other violation of, any third party rights.</p>
<h3 style="box-sizing: border-box; font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif; line-height: 1.1; color: #333333; margin: 0px 0px 1em; font-size: 16px; background-color: #ffffff;"><span style="box-sizing: border-box;">Reservation of Rights</span></h3>
<p style="box-sizing: border-box; margin: 1em 0px; color: #333333; font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 16px; background-color: #ffffff;">We reserve the right to request that you remove all links or any particular link to our Website. You approve to immediately remove all links to our Website upon request. We also reserve the right to amen these terms and conditions and it\'s linking policy at any time. By continuously linking to our Website, you agree to be bound to and follow these linking terms and conditions.</p>
<h3 style="box-sizing: border-box; font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif; line-height: 1.1; color: #333333; margin: 0px 0px 1em; font-size: 16px; background-color: #ffffff;"><span style="box-sizing: border-box;">Removal of links from our website</span></h3>
<p style="box-sizing: border-box; margin: 1em 0px; color: #333333; font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 16px; background-color: #ffffff;">If you find any link on our Website that is offensive for any reason, you are free to contact and inform us any moment. We will consider requests to remove links but we are not obligated to or so or to respond to you directly.</p>
<p style="box-sizing: border-box; margin: 1em 0px; color: #333333; font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 16px; background-color: #ffffff;">We do not ensure that the information on this website is correct, we do not warrant its completeness or accuracy; nor do we promise to ensure that the website remains available or that the material on the website is kept up to date.</p>
<h3 style="box-sizing: border-box; font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif; line-height: 1.1; color: #333333; margin: 0px 0px 1em; font-size: 16px; background-color: #ffffff;"><span style="box-sizing: border-box;">Disclaimer</span></h3>
<p style="box-sizing: border-box; margin: 1em 0px; color: #333333; font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 16px; background-color: #ffffff;">To the maximum extent permitted by applicable law, we exclude all representations, warranties and conditions relating to our website and the use of this website. Nothing in this disclaimer will:</p>
<ul style="box-sizing: border-box; margin: 1em 0px; padding: 0px 0px 0px 1em; list-style-position: outside; color: #333333; font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 16px; background-color: #ffffff;">
<li style="box-sizing: border-box; margin-top: 0.5em; margin-bottom: 0.5em;">limit or exclude our or your liability for death or personal injury;</li>
<li style="box-sizing: border-box; margin-top: 0.5em; margin-bottom: 0.5em;">limit or exclude our or your liability for fraud or fraudulent misrepresentation;</li>
<li style="box-sizing: border-box; margin-top: 0.5em; margin-bottom: 0.5em;">limit any of our or your liabilities in any way that is not permitted under applicable law; or</li>
<li style="box-sizing: border-box; margin-top: 0.5em; margin-bottom: 0.5em;">exclude any of our or your liabilities that may not be excluded under applicable law.</li>
</ul>
<p style="box-sizing: border-box; margin: 1em 0px; color: #333333; font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 16px; background-color: #ffffff;">The limitations and prohibitions of liability set in this Section and elsewhere in this disclaimer: (a) are subject to the preceding paragraph; and (b) govern all liabilities arising under the disclaimer, including liabilities arising in contract, in tort and for breach of statutory duty.</p>
<p style="box-sizing: border-box; margin: 1em 0px; color: #333333; font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 16px; background-color: #ffffff;">As long as the website and the information and services on the website are provided free of charge, we will not be liable for any loss or damage of any nature.</p>','link_relation' => NULL,'target' => NULL,'active_key' => 'test','meta_title' => 'Terms and Conditions','meta_keywords' => 'Terms and Conditions','meta_description' => 'Terms and Conditions','meta_robots' => 'index, follow','meta_canonical' => NULL,'created_at' => '2020-06-30 12:40:36','updated_at' => '2020-07-02 13:37:37','deleted_at' => NULL),
            array('page_id' => '2','lang_id' => '1','name' => 'Privacy Policy','title' => 'Privacy Policy','description' => NULL,'page_content' => '<h2>Privacy Policy</h2>
<p>Your privacy is important to us. It is Xyz Site\'s policy to respect your privacy regarding any information we may collect from you across our website, <a href="http://xyzsite.in">http://xyzsite.in</a>, and other sites we own and operate.</p>
<p>We only ask for personal information when we truly need it to provide a service to you. We collect it by fair and lawful means, with your knowledge and consent. We also let you know why we&rsquo;re collecting it and how it will be used.</p>
<p>We only retain collected information for as long as necessary to provide you with your requested service. What data we store, we&rsquo;ll protect within commercially acceptable means to prevent loss and theft, as well as unauthorized access, disclosure, copying, use or modification.</p>
<p>We don&rsquo;t share any personally identifying information publicly or with third-parties, except when required to by law.</p>
<p>Our website may link to external sites that are not operated by us. Please be aware that we have no control over the content and practices of these sites, and cannot accept responsibility or liability for their respective privacy policies.</p>
<p>You are free to refuse our request for your personal information, with the understanding that we may be unable to provide you with some of your desired services.</p>
<p>Your continued use of our website will be regarded as acceptance of our practices around privacy and personal information. If you have any questions about how we handle user data and personal information, feel free to contact us.</p>
<p>This policy is effective as of 2 July 2020.</p>','link_relation' => NULL,'target' => NULL,'active_key' => 'privacy-policy','meta_title' => NULL,'meta_keywords' => NULL,'meta_description' => NULL,'meta_robots' => NULL,'meta_canonical' => NULL,'created_at' => '2020-07-02 10:37:07','updated_at' => '2020-07-05 09:25:25','deleted_at' => NULL),
            array('page_id' => '3','lang_id' => '1','name' => 'Pause a Loop','title' => 'Pause a Loop','description' => 'Well I was thinking same as you. Is it possible to pause a loop in javascript? I\'ve searched Google but could not find anything useful. But i thought that again is it really possible to pause a loop. The answer is No! But.. wait... it does not mean that we could not achieve what we want. We can\'t give up easily. There is a work around. See below.','page_content' => '<p>
Well I was thinking same as you. Is it possible to pause a loop in javascript? I\'ve searched Google but could not find anything useful. But i thought that again is it really possible to pause a loop. The answer is No! But.. wait... it does not mean that we could not achieve what we want. We can\'t give up easily. There is a work around. See below. 
</p>

<h1>How can i pause in for loop. (Work around)</h1>
<p>
As i mention its not possible but we can achieve this. Let see how we can.  
</p>
<code>
<pre>
&lt;script type="text/javascript"&gt;
var cnt = 0;
function myTask(i, j) {
	//alert("My Task "+ (m++));
	var p = "&lt;p> " +i + j+" my task "+(++cnt)+"&lt;/p>"
	document.getElementById(\'logDiv\').innerHTML = document.getElementById(\'logDiv\').innerHTML + p;
}

function myLoop(start, end, fn, delay) {
	for(var i=start;i&lt;end;i++) (function(i) {
		setTimeout(function() {
			fn.call(this, arguments);
		}, i*delay);
	})(i);
}
myLoop(0, 10, function() {
	myTask("This ", "is ");
}, 2000);
&lt;/script&gt;
</pre>
</code>

<script type="application/javascript">
var cnt = 0;
function myTask(i, j) {
	//alert("My Task "+ (m++));
	var p = "<p> " +i + j+" my task "+(++cnt)+"</p>"
	document.getElementById(\'logDiv\').innerHTML = document.getElementById(\'logDiv\').innerHTML + p;
}

function myLoop(start, end, fn, delay) {
	for(var i=start;i<end;i++) (function(i) {
		setTimeout(function() {
			fn.call(this, arguments);
		}, i*delay);
	})(i);
}


function startMyLoop(){
myLoop(0, 10, function() {
	myTask("This ", "is ");
}, 2000);

}
</script>
<input type="button" value="Run my loop" onclick="startMyLoop()" />
<div id="logDiv" style="border:1px solid #111; padding:10px; margin-top:10px">
<p>I am log div</p>
</div>
<p>&nbsp;</p>','link_relation' => NULL,'target' => NULL,'active_key' => 'pause-a-loop','meta_title' => 'How can I pause a loop?','meta_keywords' => 'how, can, pause, loop, javascript','meta_description' => 'How can i pause a loop? There is a work around.','meta_robots' => NULL,'meta_canonical' => NULL,'created_at' => '2020-07-03 08:01:08','updated_at' => '2020-07-10 03:56:25','deleted_at' => NULL),
            array('page_id' => '4','lang_id' => '1','name' => 'Test Blog','title' => 'Test Blog','description' => 'How to override a native method of JavaScript?','page_content' => 'How to override a native method of JavaScript?
Content goes here','link_relation' => NULL,'target' => NULL,'active_key' => 'test-blog','meta_title' => NULL,'meta_keywords' => NULL,'meta_description' => NULL,'meta_robots' => NULL,'meta_canonical' => NULL,'created_at' => '2020-07-11 10:47:26','updated_at' => '2020-07-11 10:47:26','deleted_at' => NULL)
        );


        if(DB::table($table_name)->get()->count() == 0) {
            DB::table($table_name)->insert($pages);
            DB::table($table_name_langs)->insert($page_langs);
        } else {
            echo "SeedingError: `$table_name` table is not empty\n";
        }
    }
}
