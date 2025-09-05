import{d as p,ab as _,e as n,o as t,G as i,I as k,E as s,c as y,B as g,A as x,F as f,u as r,g as e,N as b,a as v}from"./app-GeWjX9nR.js";import{c as o}from"./createLucideIcon-BoJrWEEx.js";import{_ as M,a as A}from"./Layout.vue_vue_type_script_setup_true_lang-Bjvshy9m.js";import"./Heading.vue_vue_type_script_setup_true_lang-pWtCqrca.js";import"./index-BZs4ypES.js";import"./index-H4X_E94e.js";/**
 * @license lucide-vue-next v0.468.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const I=o("MonitorIcon",[["rect",{width:"20",height:"14",x:"2",y:"3",rx:"2",key:"48i651"}],["line",{x1:"8",x2:"16",y1:"21",y2:"21",key:"1svkeh"}],["line",{x1:"12",x2:"12",y1:"17",y2:"21",key:"vw1qmm"}]]);/**
 * @license lucide-vue-next v0.468.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const w=o("MoonIcon",[["path",{d:"M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z",key:"a7tn18"}]]);/**
 * @license lucide-vue-next v0.468.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const C=o("SunIcon",[["circle",{cx:"12",cy:"12",r:"4",key:"4exip2"}],["path",{d:"M12 2v2",key:"tus03m"}],["path",{d:"M12 20v2",key:"1lh1kg"}],["path",{d:"m4.93 4.93 1.41 1.41",key:"149t6j"}],["path",{d:"m17.66 17.66 1.41 1.41",key:"ptbguv"}],["path",{d:"M2 12h2",key:"1t8f8n"}],["path",{d:"M20 12h2",key:"1q8mjw"}],["path",{d:"m6.34 17.66-1.41 1.41",key:"1m8zz5"}],["path",{d:"m19.07 4.93-1.41 1.41",key:"1shlcs"}]]),B={class:"inline-flex gap-1 rounded-lg bg-neutral-100 p-1 dark:bg-neutral-800"},S=["onClick"],$={class:"ml-1.5 text-sm"},z=p({__name:"AppearanceTabs",setup(d){const{appearance:c,updateAppearance:l}=_(),u=[{value:"light",Icon:C,label:"Light"},{value:"dark",Icon:w,label:"Dark"},{value:"system",Icon:I,label:"System"}];return(N,j)=>(t(),n("div",B,[(t(),n(i,null,k(u,({value:a,Icon:m,label:h})=>s("button",{key:a,onClick:q=>r(l)(a),class:f(["flex items-center rounded-md px-3.5 py-1.5 transition-colors",r(c)===a?"bg-white shadow-xs dark:bg-neutral-700 dark:text-neutral-100":"text-neutral-500 hover:bg-neutral-200/60 hover:text-black dark:text-neutral-400 dark:hover:bg-neutral-700/60"])},[(t(),y(g(m),{class:"-ml-1 h-4 w-4"})),s("span",$,x(h),1)],10,S)),64))]))}}),D={class:"space-y-6"},L={breadcrumbs:[{title:"Settings",href:route("settings")},{title:"Appearance",href:route("appearance")}]},Z=p({...L,__name:"Appearance",setup(d){return(c,l)=>(t(),n(i,null,[e(r(b),{title:"Appearance settings"}),e(A,null,{default:v(()=>[s("div",D,[e(M,{title:"Appearance settings",description:"Update your account's appearance settings"}),e(z)])]),_:1})],64))}});export{Z as default};
