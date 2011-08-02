var $dabaGaleria=jQuery.noConflict();
$dabaGaleria(document).ready(function(){
    Galleria.loadTheme('wp-content/plugins/DaBaDisseny-Galeria/galleria/themes/classic/galleria.classic.js');
    $dabaGaleria(".daba-galeria").each(function(){
        var w=$dabaGaleria(this).width();
        var h=$dabaGaleria(this).height();
        $dabaGaleria(this).galleria({
            width: w,
            height: h
        });

    });
    $dabaGaleria('.daba-galeria:odd').addClass('dabaGaleriaSenar');
    $dabaGaleria('.daba-galeria:even').addClass('dabaGaleriaParell');
});


