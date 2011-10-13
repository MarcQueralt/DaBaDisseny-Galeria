var $dabaGaleria=jQuery.noConflict();
$dabaGaleria(document).ready(function(){
    var hostname = window.location.hostname;
    var protocol = window.location.protocol;
    var destination = protocol+'//'+hostname+'/'+'wp-content/plugins/DaBaDisseny-Galeria/galleria/themes/classic/galleria.classic.js';
    Galleria.loadTheme(destination);
    $dabaGaleria(".daba-galeria").each(function(){
        var w=$dabaGaleria(this).width();
        var h=$dabaGaleria(this).height();
        $dabaGaleria(this).galleria({
            width: w,
            height: h,
            autoplay: 5000,
            debug: false
        });
    });
    $dabaGaleria('.daba-galeria:odd').addClass('dabaGaleriaSenar');
    $dabaGaleria('.daba-galeria:even').addClass('dabaGaleriaParell');
});


