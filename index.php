<?php
/**
 * index.php for Multisite Language Switcher Theme.
 * v1.1 by oncleben31 and Namide
 * 
 * Description: This theme redirect users to the languages accepted by the browser
 * The Theme redirect the 404 error page too.
 * This theme is a quick and dirty hack.
 * You must install the plugin Multisite Language Switcher to use it.
 */


/*
 * Get Prefered language of the browser
 */
if (!isset($lang))
{
	$acceptedLanguages = filter_var($_SERVER['HTTP_ACCEPT_LANGUAGE'], FILTER_SANITIZE_STRING);
	$lang = explode(',',$acceptedLanguages);
	$lang = strtolower(substr(chop($lang[0]),0,2));
}
if (!isset($req))
{
	$req = filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_STRING);
}


/**
 * List of the sites URL by languages
 */
$sites = array();
foreach ( MslsBlogCollection::instance()->get_objects() as $key => $blog ) {
	
	if ( get_blog_details()->blog_id  !=  $key  )
	{
		$lg = $blog->get_alpha2();
		if (isset($sites[$lg]))
		{
			echo 'Language ' . $lg . ' has multiples solutions, please fix that';
			exit();
		}
		$sites[$lg] = get_blog_details($key)->path;
	}
}


/**
 * Run redirection
 * 
 * @param string $lang		Current language
 * @param string $req		Current path
 * @param array $sites		List of others sites
 */
function redirectTo( $lang, $req, &$sites )
{
	if ( $req == get_blog_details()->path )
	{
		header('Location: '.$sites[$lang]);
	}
	else
	{
		header('Location: '.$sites[$lang].'404error');
	}
}


/**
 * Redirect to equivalent of the navigator language
 */
if (isset($sites[$lang]))
{
	redirectTo( $lang, $req, $sites );
}
/**
 * Redirect to the english page
 */
else if (isset($sites['en']))
{
	redirectTo( 'en', $req, $sites );
}
/**
 * Redirect to the first site
 */
else if (count($sites) > 0)
{
	redirectTo(array_keys($sites)[0], $req, $sites );
}
/**
 * No sites founds
 */
else
{
	echo 'configure languages of your website: configure Multisite Language Switcher';
}
