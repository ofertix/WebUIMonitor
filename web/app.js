/*
 * This file is part of the WebUIMonitor package.
 *
 * (c) Joan Valduvieco <joan.valduvieco@ofertix.com>
 * (c) Jordi Llonch <jordi.llonch@ofertix.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Ext.Loader.setConfig({enabled:true});

Ext.application({
    name:'Monitor',

    appFolder:'app',

    controllers:[
        'UpstreamError'
    ],

    launch:function () {
        Ext.create('Ext.container.Viewport', {
            layout:'fit',

            items:[
                {
                    xtype:'upstreamerrorlist',
                    title:'My Project Monitor'
                }
            ]
        });
    }
});