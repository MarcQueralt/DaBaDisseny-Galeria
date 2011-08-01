var $dabaGaleria=jQuery.noConflict();
$dabaGaleria(document).ready(function(){
    Galleria.loadTheme('wp-content/plugins/DaBaDisseny-Galeria/galleria/themes/classic/galleria.classic.js');
    $dabaGaleria(".daba-galeria").each(function(){
        var w=$dabaGaleria(this).width();
        var h=$dabaGaleria(this).height();
        alert(w);
        alert(h);
    });
//    $dabaGaleria(".daba-galeria").galleria({
//        width: 300,
//        height: 300
//    });
});


