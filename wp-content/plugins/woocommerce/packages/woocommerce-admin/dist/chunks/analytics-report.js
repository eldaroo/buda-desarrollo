(window.__wcAdmin_webpackJsonp=window.__wcAdmin_webpackJsonp||[]).push([[9],{598:function(e,t,r){"use strict";r.d(t,"b",(function(){return u})),r.d(t,"a",(function(){return f}));var n=r(0),o=r(141),c=r(247),i=r.n(c),a=r(85),s=i()(a.b),u=function(e){var t=s.getCurrencyConfig(),r=Object(o.applyFilters)("woocommerce_admin_report_currency",t,e);return i()(r)},f=Object(n.createContext)(s)},599:function(e,t,r){"use strict";r(64);var n=r(22),o=r.n(n),c=r(23),i=r.n(c),a=r(24),s=r.n(a),u=r(25),f=r.n(u),p=r(14),l=r.n(p),b=r(0),y=r(2),m=r(1),O=r.n(m),h=r(145),d=r(85);function j(e){var t=function(){if("undefined"==typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"==typeof Proxy)return!0;try{return Boolean.prototype.valueOf.call(Reflect.construct(Boolean,[],(function(){}))),!0}catch(e){return!1}}();return function(){var r,n=l()(e);if(t){var o=l()(this).constructor;r=Reflect.construct(n,arguments,o)}else r=n.apply(this,arguments);return f()(this,r)}}var v=function(e){s()(r,e);var t=j(r);function r(){return o()(this,r),t.apply(this,arguments)}return i()(r,[{key:"render",value:function(){var e,t,r,n,o=this.props,c=o.className,i=o.isError,a=o.isEmpty;return i?(e=Object(y.__)("There was an error getting your stats. Please try again.",'woocommerce'),t=Object(y.__)("Reload",'woocommerce'),n=function(){window.location.reload()}):a&&(e=Object(y.__)("No results could be found for this date range.",'woocommerce'),t=Object(y.__)("View Orders",'woocommerce'),r=Object(d.f)("edit.php?post_type=shop_order")),Object(b.createElement)(h.EmptyContent,{className:c,title:e,actionLabel:t,actionURL:r,actionCallback:n})}}]),r}(b.Component);v.propTypes={className:O.a.string,isError:O.a.bool,isEmpty:O.a.bool},v.defaultProps={className:""},t.a=v},637:function(e,t,r){},691:function(e,t,r){"use strict";r.r(t);r(64),r(53),r(60),r(49),r(61);var n=r(7),o=r.n(n),c=r(22),i=r.n(c),a=r(23),s=r.n(a),u=r(24),f=r.n(u),p=r(25),l=r.n(p),b=r(14),y=r.n(b),m=r(0),O=(r(135),r(88),r(170),r(41),r(37),r(139),r(65)),h=r(26),d=r(1),j=r.n(d),v=r(5),g=r(50),w=r(59),_=(r(637),r(599)),E=r(598),R=r(277);function P(e,t){var r=Object.keys(e);if(Object.getOwnPropertySymbols){var n=Object.getOwnPropertySymbols(e);t&&(n=n.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),r.push.apply(r,n)}return r}function k(e){for(var t=1;t<arguments.length;t++){var r=null!=arguments[t]?arguments[t]:{};t%2?P(Object(r),!0).forEach((function(t){o()(e,t,r[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(r)):P(Object(r)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(r,t))}))}return e}function C(e){var t=function(){if("undefined"==typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"==typeof Proxy)return!0;try{return Boolean.prototype.valueOf.call(Reflect.construct(Boolean,[],(function(){}))),!0}catch(e){return!1}}();return function(){var r,n=y()(e);if(t){var o=y()(this).constructor;r=Reflect.construct(n,arguments,o)}else r=n.apply(this,arguments);return l()(this,r)}}var q=function(e){var t=e.params,r=e.path;return t.report||r.replace(/^\/+/,"")},S=function(e){f()(r,e);var t=C(r);function r(){var e;return i()(this,r),(e=t.apply(this,arguments)).state={hasError:!1},e}return s()(r,[{key:"componentDidCatch",value:function(e){this.setState({hasError:!0}),console.warn(e)}},{key:"render",value:function(){if(this.state.hasError)return null;if(this.props.isError)return Object(m.createElement)(_.a,{isError:!0});var e=q(this.props),t=Object(v.find)(Object(R.a)(),{report:e});if(!t)return null;var r=t.component;return Object(m.createElement)(E.a.Provider,{value:Object(E.b)(Object(g.getQuery)())},Object(m.createElement)(r,this.props))}}]),r}(m.Component);S.propTypes={params:j.a.object.isRequired},t.default=Object(O.compose)(Object(h.withSelect)((function(e,t){var r=Object(g.getQuery)();if(!r.search)return{};var n=q(t),c=Object(g.getSearchWords)(r),i="categories"===n&&"single_category"===r.filter?"products":n,a=Object(w.searchItemsByString)(e,i,c),s=a.isError,u=a.isRequesting,f=a.items,p=Object.keys(f);return p.length?{isError:s,isRequesting:u,query:k(k({},t.query),{},o()({},i,p.join(",")))}:{isError:s,isRequesting:u}})))(S)}}]);