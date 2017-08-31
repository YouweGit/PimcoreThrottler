pimcore.registerNS("pimcore.plugin.pimcorethrottler");

pimcore.plugin.pimcorethrottler = Class.create(pimcore.plugin.admin, {
    getClassName: function() {
        return "pimcore.plugin.pimcorethrottler";
    },

    initialize: function() {
        pimcore.plugin.broker.registerPlugin(this);
    },
 
    pimcoreReady: function (params,broker){
        // alert("PimcoreThrottler Plugin Ready!");
    }
});

var pimcorethrottlerPlugin = new pimcore.plugin.pimcorethrottler();

