(this.webpackJsonpclient=this.webpackJsonpclient||[]).push([[0],{31:function(e,t,a){e.exports=a(60)},36:function(e,t,a){},37:function(e,t,a){},60:function(e,t,a){"use strict";a.r(t);var n=a(0),l=a.n(n),s=a(29),r=a.n(s),c=(a(36),a(15)),o=a(1),u=(a(37),a(7)),i=a(8),m=a(10),d=a(9),h=function(e){Object(m.a)(a,e);var t=Object(d.a)(a);function a(){return Object(u.a)(this,a),t.apply(this,arguments)}return Object(i.a)(a,[{key:"render",value:function(){return l.a.createElement("nav",{className:"navbar navbar-expand-lg navbar-light bg-light"},l.a.createElement("div",{className:"container"},l.a.createElement("a",{className:"navbar-brand",href:"#"},"Fsoftpro"),l.a.createElement("button",{className:"navbar-toggler",type:"button","data-toggle":"collapse","data-target":"#navbarSupportedContent","aria-controls":"navbarSupportedContent","aria-expanded":"false","aria-label":"Toggle navigation"},l.a.createElement("span",{className:"navbar-toggler-icon"})),l.a.createElement("div",{className:"collapse navbar-collapse",id:"navbarSupportedContent"},l.a.createElement("ul",{className:"navbar-nav mr-auto"},l.a.createElement("li",{className:"nav-item active"},l.a.createElement(c.b,{to:"/",className:"nav-link"},"Home"))))))}}]),a}(n.Component),v=a(14),b=a.n(v),p=a(11),E=function(e){Object(m.a)(a,e);var t=Object(d.a)(a);function a(e){var n;return Object(u.a)(this,a),(n=t.call(this,e)).state={status:null,message:""},n.handleClick=n.handleClick.bind(Object(p.a)(n)),n}return Object(i.a)(a,[{key:"componentDidMount",value:function(){this.setState({status:null,message:""})}},{key:"handleClick",value:function(){var e=this;this.setState({status:null,message:""});b.a.post("http://localhost:5000/truncate").then((function(t){console.log(t),e.setState({status:"success",message:"TRUNCATE all success"})})).catch((function(t){e.setState({status:"error",message:"Fail truncate."})}))}},{key:"render",value:function(){return l.a.createElement("div",null,"success"==this.state.status?l.a.createElement("div",{className:"alert alert-success",role:"alert"},this.state.message):"","error"==this.state.status?l.a.createElement("div",{className:"alert alert-danger",role:"alert"},this.state.message):"",l.a.createElement("button",{type:"button",className:"btn btn-outline-primary",onClick:this.handleClick},"Reset (TRUNCATE)"))}}]),a}(n.Component),g=function(e){Object(m.a)(a,e);var t=Object(d.a)(a);function a(e){var n;return Object(u.a)(this,a),(n=t.call(this,e)).state={status:null,message:"",date:""},n.handleClick=n.handleClick.bind(Object(p.a)(n)),n.handleChange=n.handleChange.bind(Object(p.a)(n)),n}return Object(i.a)(a,[{key:"componentDidMount",value:function(){this.setState({status:null,message:"",date:""})}},{key:"handleChange",value:function(e){this.setState({date:e.target.value})}},{key:"handleClick",value:function(){var e=this;this.setState({status:null,message:""});var t={date:this.state.date};b.a.post("http://localhost:5000/setdate",t).then((function(t){console.log(t),e.setState({status:"success",message:t.data})})).catch((function(t){e.setState({status:"error",message:t.data})}))}},{key:"render",value:function(){return l.a.createElement("div",null,"success"==this.state.status?l.a.createElement("div",{className:"alert alert-success",role:"alert"},this.state.message):"","error"==this.state.status?l.a.createElement("div",{className:"alert alert-danger",role:"alert"},this.state.message):"",l.a.createElement("div",{className:"input-group mb-3"},l.a.createElement("input",{type:"date",className:"form-control",onChange:this.handleChange}),l.a.createElement("div",{className:"input-group-append"},l.a.createElement("button",{className:"btn btn-outline-primary",type:"button",onClick:this.handleClick},"Update"))))}}]),a}(n.Component),f=function(e){Object(m.a)(a,e);var t=Object(d.a)(a);function a(){return Object(u.a)(this,a),t.apply(this,arguments)}return Object(i.a)(a,[{key:"render",value:function(){return l.a.createElement("div",{className:"container"},l.a.createElement("div",{className:"row py-3"},l.a.createElement("div",{className:"col-12"},l.a.createElement("table",{className:"table table-bordered"},l.a.createElement("thead",null,l.a.createElement("tr",null,l.a.createElement("th",null,"Name"),l.a.createElement("th",null))),l.a.createElement("tbody",null,l.a.createElement("tr",null,l.a.createElement("td",null,"Turncate all for reset new import data"),l.a.createElement("td",null,l.a.createElement(E,null))),l.a.createElement("tr",null,l.a.createElement("td",null,"Set date"),l.a.createElement("td",null,l.a.createElement(g,null))))))))}}]),a}(n.Component);var N=function(){return l.a.createElement("div",null,l.a.createElement(c.a,null,l.a.createElement("div",null,l.a.createElement(h,null),l.a.createElement(o.c,null,l.a.createElement(o.a,{path:"/home"},l.a.createElement(f,null)),l.a.createElement(o.a,{path:"/"},l.a.createElement(f,null))))))};Boolean("localhost"===window.location.hostname||"[::1]"===window.location.hostname||window.location.hostname.match(/^127(?:\.(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)){3}$/));r.a.render(l.a.createElement(l.a.StrictMode,null,l.a.createElement(N,null)),document.getElementById("root")),"serviceWorker"in navigator&&navigator.serviceWorker.ready.then((function(e){e.unregister()})).catch((function(e){console.error(e.message)}))}},[[31,1,2]]]);
//# sourceMappingURL=main.06947eef.chunk.js.map