!function(){"use strict";!function e(){if("loading"===document.readyState)return document.addEventListener("DOMContentLoaded",e);!function(e){var t=e.querySelectorAll("tbody > tr"),n=document.createElement("td"),r=document.createElement("th"),a=document.querySelectorAll("input, select");r.innerHTML=webcomicGeneratorL10n.publish,e.querySelector("thead tr").appendChild(r);for(var o=0;o<t.length;o++)t[o].appendChild(n.cloneNode());for(var c=function(r){if(a[r].addEventListener("change",function(){setTimeout(function(){return e=document.querySelector("#webcomic_generator"),t=new FormData(e),a=new XMLHttpRequest,t.append("action","webcomic_generator_preview"),a.onreadystatechange=function(){if(4===a.readyState){for(var e=document.querySelectorAll("table.media tbody > tr td:last-child"),t=[],n=0;n<e.length;n++)e[n].innerHTML="&mdash;";if(a.responseText){t=JSON.parse(a.responseText);for(var r=0;r<t.length;r++)document.querySelector('table.media tbody [data-id="'.concat(t[r].id,'"] td:last-child')).innerHTML=t[r].date}}},a.open("POST",ajaxurl),void a.send(t);var e,t,a},1)}),"webcomic_generator[start_date]"!==a[r].name)return"continue";a[r].addEventListener("change",function(){return e=a[r],t=new FormData,n=new XMLHttpRequest,t.append("action","webcomic_generator_start_date"),t.append("date",e.value),n.onreadystatechange=function(){4===n.readyState&&(document.querySelector('label[for="'.concat(e.name,'"]')).innerHTML=JSON.parse(n.responseText)[0])},n.open("POST",ajaxurl),void n.send(t);var e,t,n}),a[r].dispatchEvent(new Event("change"))},d=0;d<a.length;d++)c(d)}(document.querySelector("table.media")),jQuery("table.media tbody").sortable({items:"tr",start:function(e,t){t.placeholder.height(t.helper.outerHeight())},stop:function(){document.querySelector('[name="webcomic_generator[collection]"]').dispatchEvent(new Event("change"))}})}()}();
