/*
 * This file is part of the WebUIMonitor package.
 *
 * (c) Joan Valduvieco <joan.valduvieco@ofertix.com>
 * (c) Jordi Llonch <jordi.llonch@ofertix.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Ext.define('Monitor.model.UpstreamError', {
    extend: 'Ext.data.Model',
    fields: ['id', 'date', 'name', 'error_type', 'error_severity', 'msg', 'upstream', 'request', 'referrer', 'server', 'host', 'client']
});
