<?php

/*
  Plugin Name: DaBa-Galeria
  Description: A plugin based on <a href="http://wordpress.org/extend/plugins/easy-picasa/" target="_Blank">Easy Picasa 1.1 by YiXia</a> and <a href="http://galleria.aino.se/" target="_blank">aino gallery</a> to fetch and show picasa pictures with multilingual text description. 
  Version: 1.0
  Author: DaBa Disseny
  Author URI: http://www.dabadisseny.com
 */

define( "BACKGROUND", "ffffff" );
define( "PLUGINURI", WP_CONTENT_URL . '/plugins/' . dirname( plugin_basename( __FILE__ ) ) );

function picasa_diapositives( $atts, $url )
{
//[picasa width="300" height="300" background="ffffff" autoplay="1" showcaption="1"]http://picasaweb.google.com/abttong/KTnkC02[/picasa]
    $defaults = array(
        'width' => '300',
        'height' => '300',
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
    $result = '<div class="daba-galeria" style="overflow:hidden; height:'.$height.'px; width:'.$width.'px; max-height:'.$height.'px; max-width:'.$width.'px;">';
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
            try
            {
                $titol = $photoEntry->getTitle()->getText();
            } catch ( Exception $e )
            {
                $titol = '';
            }
            $fotoGegant = $mediaContentArray[0]->getUrl();
            try
            {
                $fotos=$photoEntry->getMediaGroup()->getThumbnail();
                $foto = $fotos[2];
                $foto=$foto->getUrl();
            } catch ( Exception $e )
            {
                $foto = $fotoGegant;
            }
            try
            {
                $thumbnails = $photoEntry->getMediaGroup()->getThumbnail();
                $thumbnail = $thumbnails[0]->getUrl();
            } catch ( Exception $e )
            {
                $thumbnail=$foto;
            }
            if ( $lang != '' ) :
                $patro='/\[' . $lang . '\](.*)\[\/' . $lang . '\]/';
                preg_match( $patro, $summary, $matchesSummary );
                if ( isset( $matchesSummary[1] ) ):
                    $summary = $matchesSummary[1];
                endif;
                preg_match($patro, $titol, $matchesTitol);
                if(isset ($matchesTitol[1])):
                    $titol=$matchesTitol[1];
                endif;
            endif;
            $result.='<a rel="' . $foto . '" href="' . $fotoGegant . '"/>';
            $result.= '<img src="' . $thumbnail
                    . '" alt="' . $summary
                    . '" title="' . $titol
                    . '"/>';
            $result.='</a>';
        }
    } catch ( Exception $e )
    {
        $result.='';
    }
    $result.='</div><!--daba-galeria-->';
    $gp=null;
    return $result;
}

function picasa_diapositives_add_mediabutton()
{
    $imgsrc = PLUGINURI . '/picasaicon.gif';
    $href = PLUGINURI . '/picasa.html?&amp;TB_iframe=true&amp;height=500&amp;width=750';
    $buttontips = __( 'Insert Picasa Photo(s)' );
    echo "<a class='thickbox' title='Add Picasa Image' id='easypicasa' href='$href'><img src='$imgsrc' alt='$buttontips' tip='$buttontips' /></a>";
}

load_plugin_textdomain( 'daba-galeria', false, $plugin_dir . '/languages' );
add_shortcode( 'picasa', 'picasa_diapositives' );
add_action( 'media_buttons', 'picasa_diapositives_add_mediabutton', 20 );
wp_register_script( 'daba-galeria', plugin_dir_url( __FILE__ ) . 'galleria/galleria-1.2.4.js', 'jquery', '', true );
wp_enqueue_script( 'daba-galeria' );
wp_register_script( 'daba-galeria-inici', plugin_dir_url( __FILE__ ) . 'daba-galeria.js', 'daba-galeria', '', true );
wp_enqueue_script( 'daba-galeria-inici' );