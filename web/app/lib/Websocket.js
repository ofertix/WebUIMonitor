/*
 * This file is part of the WebUIMonitor package.
 *
 * (c) Joan Valduvieco <joan.valduvieco@ofertix.com>
 * (c) Jordi Llonch <jordi.llonch@ofertix.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Ext.define('Monitor.lib.Websocket', {

  config: {
    url: null,
    events: {
      onOpen: null,
      onClose: null,
      onError: null,
      onMessage: null
    },
    supervisorInterval: 1000,
    scope: this
  },

  wsocket: null,

  constructor: function(config) {
    this.initConfig(config);

    return this;
  },

  start: function() {
    try {
      if ( Ext.isGecko )
      {
        this.wsocket = new MozWebSocket(this.url);
      }
      else
      {
        this.wsocket = new WebSocket(this.url);
      }
    } catch(ex) {
      console.log('exception')
      console.log(ex);
    }

    // insert this on websocket object
    this.wsocket.scope = this;

    this.wsocket.onopen = this.onOpen;
    this.wsocket.onmessage = this.onMessage;
    this.wsocket.close = this.onClose;
    this.wsocket.error = this.onError;

    // run supervisor
    this.supervisor();
  },

  onOpen: function() {
    // note: this => is Websocket scope
    console.log('online');
    if(this.scope.events.onOpen) this.scope.events.onOpen(this.scope.scope);
  },

  onClose: function() {
    // note: this => is Websocket scope
    console.log('close');
    if(this.scope.events.onClose) this.scope.events.onClose(this.scope.scope);
  },

  onError: function() {
    // note: this => is Websocket scope
    console.log('error');
    if(this.scope.events.onError) this.scope.events.onError(this.scope.scope);
  },

  onMessage: function(msg) {
    // note: this => is Websocket scope
    console.log('onmessage: ' + msg.data);
    if(this.scope.events.onMessage) this.scope.events.onMessage(this.scope.scope, msg);
  },

  supervisor: function() {
    console.log('supervisor started...');
    var task = {
      run: function(){
        if ( Ext.isGecko ) closed = MozWebSocket.CLOSED;
        else var closed = WebSocket.CLOSED;
        if(this.wsocket.readyState == closed)
        {
          console.log('reconnecting websocket...');
          this.start();
        }
      },
      interval: 1000, //1 second
      scope: this
    }
    Ext.TaskManager.start(task);
  }


});
