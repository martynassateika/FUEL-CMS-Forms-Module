<h1>Forms Module Documentation</h1>
<p>This Forms module documentation is for version <?=FORMS_VERSION?>.</p>

<p>The Forms module gives users the ability to create common forms. It has the following features:</p>
<ul>
	<li>Create forms directly in the CMS or by using a block view file</li>
	<li>Provides several additional <a href="<?=user_guide_url('general/forms')?>">custom field types</a> that can be used for combatting SPAM including a <a href="http://www.dexmedia.com/blog/honeypot-technique/" target="_blank">honeypot</a>, a simple equation, <a href="https://www.google.com/recaptcha/intro/index.html" target="_blank">reCAPTCHA</a>, or <a href="http://akismet.com/" target="blank">Akismet</a></li>
	<li>Emails specified recipients upon form submission</li>
	<li>Automatically saves entries into the database which can be exported in a CSV format later</li>
	<li>Automatic validation of common fields and provides ways to add your own custom validation</li>
	<li>Both javascript submit and validation as well as server side</li>
	<li>Specify a return URL</li>
	<li>Hooks into various parts of the processing of the email</li>
</ul>

<h2>Hooks</h2>
<p>There are a number of hooks you can use to add additional processing functionality during the form submission process. You can pass it a callable function or an array with the first index being the object and the second being the method to execute on the object.
	Additionally, you can specify an array of <a href="http://ellislab.com/codeigniter/user-guide/general/hooks.html" target="_blank">CodeIgniter hook parameters</a>. 
	The hooks must be specified in the config file to be run upon processing and not on the object during rendering unless the form is submitting to the same page in which it is rendered.
	A hook gets passed 2 parameters&mdash;the first is the form object instance, the second is the $_POST parameters as a convenience.
	The following hooks are:
</p>
<ul>
	<li><strong>pre_validate</strong>: excutes right before the validation of the form fields</li>
	<li><strong>post_validate</strong>: executes after the validation of the form fields</li>
	<li><strong>pre_save</strong>: executes right before saving (if saving is enabled for the form)</li>
	<li><strong>post_save</strong>: executes right after saving (if saving is enabled for the form)</li>
	<li><strong>pre_notify</strong>: executes right before notifying a recipient</li>
	<li><strong>success</strong>: executes upon successful submission</li>
	<li><strong>error</strong>: executes on error</li>
</ul>

<p>Below is an example of adding a hook in the configuration file of a form:</p>
<pre class="brush:php">
// you can add form configurations here which can then be referenced simply by one of the following methods form('test'), $this->fuel->forms->get('test')
$config['forms']['forms'] = array(
	'contact' => array(
				'anti_spam_method' => array('method' => 'honeypot'),
				'after_submit_text' => 'Your inquiry has been successfully sent.',
				'email_recipients' => array('superman@krypton.com'),
				'javascript_submit' => TRUE,
				'javascript_validate' => TRUE,
				'form_action' => 'forms/application',
				'submit_button_text' => 'Submit',
				'reset_button_text' => 'Reset',
				'form_display' => 'block',
				'block_view' => 'application_form',
				'fields' => array(
					'fullname' => array('required' => TRUE, 'label' => 'Full Name'),
					'phone' => array('type' => 'phone', 'required' => TRUE, 'label' => 'Phone'),
					'email' => array('type' => 'email', 'required' => TRUE),
					'comments' => array('type' => 'textarea', 'rows' => 5, 'label' => 'Comments &amp; Questions'),
					),
				'hooks' => array(
					'post_validate' => 'my_post_validate_func'
					)
	)
);
</pre>

<h2>Examples</h2>
<p>There are several ways you can create forms. One is by using the CMS. The other is by specifying the parameters for the form in the forms configuration file under the "forms" configuration are.</p>

<p>Below are some examples of how to use the module:</p>

<h3>Using the 'form' helper function</h3>
<pre class="brush:php">
// load the helper ... $CI is CI object obtained from the get_instance(); and automatically passed to blocks
$CI->load->module_helper(FORMS_FOLDER, 'forms');

echo form('myform', array('fields' => array('name' => array('required' => TRUE)));
</pre>

<p>To add the form to a page in the CMS, you will need to add the "form" function to the <dfn>$config['parser_allowed_php_functions']</dfn> configuration in the <span class="file">fuel/application/config/parser.php</span> file. Then you can use the templating syntax like so:</p>
<pre class="brush:php">
{form('myform')}
</pre>

<h3>Using the 'forms' fuel object</h3>
<pre class="brush:php">
$form = $this->fuel->forms->create('myform', array('fields' => array('name' => array('required' => TRUE))));
echo $form->render();
</pre>

<h3>Adding additional validation</h3>
<pre class="brush:php">
$validation = array('name', 'is_equal_to', 'Please make sure the passwords match', array('{password}', '{password2}'));

$fields['name'] = array();
$fields['password'] = array('type' => 'password');
$fields['password2'] = array('type' => 'password', 'label' => 'Password verfied');

$form = $this->fuel->forms->create('myform', array('fields' => $fields, 'validation' => $validation));
echo $form->render();
</pre>

<h3>Customizing the HTML</h3>
<p>There are several ways to generate the HTML for the form. The first option is to use "auto" which will use the <a href="http://docs.getfuelcms.com/libraries/form_builder" target="_blank">Form_builder</a> class to generate the form based on
the fields you've specified. Fields can be specified in the CMS, or passed in under the 'fields' parameter as demonstrated in the above example. The second option is to use a block view and the third is to simply use an HTML string.
In both cases, you will automatically have several varables passed to it included a <dfn>$fields</dfn> array which contains an array of all the the rendered fields, their labels, and their "key" values. It also will pass variables of <dfn>email_field</dfn> and <dfn>email_label</dfn> where "email" is the name of the field.
You can then use the following in your block view or HTML:
</p>

<h4>Block View</h4>
<pre class="brush:php">
&lt;?php foreach($fields as $field) : ?&gt;
<div>
	&lt;?=$field['label']?&gt;
	<p>&lt;?=$field['field']?&gt;</p>
</div>
&lt;?php endforeach; ?&gt;

<!-- OR -->
<div>
	&lt;?=$name_label?&gt;
	<p>&lt;?=$name_field?&gt;</p>
</div>
<div>
	&lt;?=$email_label?&gt;
	<p>&lt;?=$email_field?&gt;</p>
</div>
</pre>

<h4>Template Syntax</h4>
<pre class="brush:php">
<div>
	{name_label}

	<p>{name_field}</p>
</div>

<div>
	{email_label}

	<p>{email_field}</p>
</div>
</pre>

<h3>Kitchen Sink</h3>
<pre class="brush:php">

$fields['name'] = array();
$fields['password'] = array('type' => 'password');
$fields['password2'] = array('type' => 'password', 'label' => 'Password verfied');

$params['fields'] = $fields;
$params['validation'] = array('name', 'is_equal_to', 'Please make sure the passwords match', array('{password}', '{password2}')); // validation rules
$params['slug'] = 'myform'; // used for form action if none is provided to submit to forms/{slug}
$params['save_entries'] = FALSE; // saves to the form_entries table
$params['form_action'] = 'http://mysite.com/signup'; // if left blank it will be submitted automatically to forms/{slug} to be processed
$params['anti_spam_method'] = array('method' => 'recaptcha', 'recaptcha_public_key' => 'xxx', 'recaptcha_private_key' => 'xxxxx', 'theme' => 'white');
$params['submit_button_text'] = 'Submit Form';
$params['reset_button_text'] = 'Reset Form';
$params['form_display'] = 'auto'; // can be 'auto', 'block', 'html'
$params['block_view'] = 'form'; // fuel/application/views/_blocks/form.php
$params['block_view_module'] = 'application'; // the name of the module the block exists (default is the main application folder)
$params['javascript_submit'] = FALSE;
$params['javascript_validate'] = TRUE;
$params['javascript_waiting_message'] = 'Submitting Information...';
$params['email_recipients'] = 'superman@krypton.com';
$params['email_cc'] = 'batman@gotham.com';
$params['email_bcc'] = 'wonderwoman@paradiseisland.com';
$params['email_subject'] = 'Website Submission';
$params['email_message'] = '{name} Just signed up!';
$params['after_submit_text'] = 'You have successfully signed up.';
$params['return_url'] = 'http://mysite.com/signup'; // only works if javascript_submit = FALSE
$params['js'] = 'myvalidation.js'; // extra javascript validation can be included

$form = $this->fuel->forms->create('myform', $params);
echo $form->render();

</pre>



<?=generate_config_info()?>


<?=generate_toc()?>

