<?php

/*
  Plugin Name: Picasa Facil
  Description: A plugin based on Easy Picasa 1.1 by YiXia.
  Version: 1.0
  Author: DaBa Disseny
  Author URI: http://www.dabadisseny.com
 */

define( "BACKGROUND", "ffffff" );
define( "PLUGINURI", WP_CONTENT_URL . '/plugins/' . dirname( plugin_basename( __FILE__ ) ) );

add_shortcode( 'picasa', 'picasa_diapositives' );

function picasa_diapositives( $atts, $url )
{
//[picasa width="400" height="400" background="ffffff" autoplay="1" showcaption="1"]http://picasaweb.google.com/abttong/KTnkC02[/picasa]
    $defaults = array(
        'width' => '400',
        'height' => '400',
        'autoplay' => '0',
        'showcaption' => '1',
        'background' => BACKGROUND,
        'lang' => ''
    );
    extract( shortcode_atts( $defaults, $atts ) );
    if ( empty( $url ) )
        return '';
    else
    {
        preg_match( "@(?:http://)?picasaweb.google.com/([\w\.-]+)/([\w-\.%]+)#?\n?@i", $url, $matches );
        if ( $matches && count( $matches ) == 3 )
        {
            $user = $matches[1];
            $album = $matches[2];
        }else
            return '';
    }
    $path = dirname( __FILE__ ) . '/library';
    set_include_path( get_include_path() . PATH_SEPARATOR . $path );
    require_once 'Zend/Loader.php';
    Zend_Loader::loadClass( 'Zend_Gdata_Photos' );
    Zend_Loader::loadClass( 'Zend_Gdata_ClientLogin' );
    Zend_Loader::loadClass( 'Zend_Gdata_AuthSub' );
    $result = '';
    try
    {
        $gp = new Zend_Gdata_Photos();
        $query = $gp->newAlbumQuery();
        $query->setUser( $user );
        $query->setAlbumName( $album );

        $albumFeed = $gp->getAlbumFeed( $query );

        foreach ( $albumFeed as $photoEntry )
        {
            $mediaContentArray = $photoEntry->getMediaGroup()->getContent();
            try
            {
                $summary = $photoEntry->getSummary()->getText();
            } catch ( Exception $e )
            {
                $summary = '';
            }
            if ( $lang != '' ) :
                preg_match( '/\[' . $lang . '\](.*)\[\/' . $lang . '\]/', $summary, $matchesSummary );
                if ( isset( $matchesSummary[1] ) ):
                    $summary = $matchesSummary[1];
                endif;
            endif;
            $result.= $photoEntry->getTitle()->getText()
                    . "&nbsp;" . $mediaContentArray[0]->getUrl()
                    . "&nbsp;" . $summary . "<br />\n";
        }
    } catch ( Exception $e )
    {
        return'';
    }
    $result .= 'Hola';
    return $result;
}

add_action( 'media_buttons', 'picasa_diapositives_add_mediabutton', 20 );

function picasa_diapositives_add_mediabutton()
{
    $imgsrc = PLUGINURI . '/picasaicon.gif';
    $href = PLUGINURI . '/picasa.html?&amp;TB_iframe=true&amp;height=500&amp;width=750';
    $buttontips = __( 'Insert Picasa Photo(s)' );
    echo "<a class='thickbox' title='Add Picasa Image' id='easypicasa' href='$href'><img src='$imgsrc' alt='$buttontips' tip='$buttontips' /></a>";
}
