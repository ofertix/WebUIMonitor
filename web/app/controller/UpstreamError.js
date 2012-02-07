/*
 * This file is part of the WebUIMonitor package.
 *
 * (c) Joan Valduvieco <joan.valduvieco@ofertix.com>
 * (c) Jordi Llonch <jordi.llonch@ofertix.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Ext.define('Monitor.controller.UpstreamError', {
  extend: 'Ext.app.Controller',

  stores: [
  'UpstreamError'
  ],
  models: [
  'UpstreamError'
  ],
  views: [
  'upstreamError.List'
  ],

  init: function() {
    console.log('initialized UpstreamError');

    this.control({
      'viewport > upstreamerrorlist dataview': {
        itemdblclick: this.copyContent
      },
      'viewport > upstreamerrorlist': {
        render: this.startWebsocket
      }
    });
    
  },
  
  startWebsocket: function() {
    var wsocket = Ext.create('Monitor.lib.WebsocketUpstreamError', {
      url: Monitor.config.App.upstreamError.websocketUrl,
      linesLimit: Monitor.config.App.upstreamError.linesLimit,
      scope: this
    });
    wsocket.start();
  },
  
  copyContent: function(grid, record) {
    var show = Ext.create('Monitor.view.upstreamError.Show').show();
    show.down('form').loadRecord(record);
  }
  
});
