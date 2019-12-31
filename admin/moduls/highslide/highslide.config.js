/**
*        Site-specific configuration settings for Highslide JS
*/
hs.graphicsDir = 'highslide/graphics/';
hs.showCredits = false;
hs.outlineType = 'custom';
hs.dimmingOpacity = 0.7;
hs.dimmingDuration = 250;
hs.easing = 'easeInCirc';
hs.align = 'center';
hs.allowSizeReduction = false;
hs.enableKeyListener = false;
hs.height = screen.height-300;
hs.width = screen.width-90;
hs.registerOverlay({
        html: '<div class="closebutton" onclick="return hs.close(this)"></div>',
        position: 'top right',
        useOnHtml: true,
        fade: 2 // fading the semi-transparent overlay looks bad in IE
});