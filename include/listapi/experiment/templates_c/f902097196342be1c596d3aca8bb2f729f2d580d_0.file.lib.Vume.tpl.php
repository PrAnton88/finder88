<?php
/* Smarty version 3.1.36, created on 2021-07-21 14:27:23
  from 'C:\Users\oto016\singleProjects\modx-info\core\Smarty\fromapi\lib.Vume.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.36',
  'unifunc' => 'content_60f812abdb3b37_82187120',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'f902097196342be1c596d3aca8bb2f729f2d580d' => 
    array (
      0 => 'C:\\Users\\oto016\\singleProjects\\modx-info\\core\\Smarty\\fromapi\\lib.Vume.tpl',
      1 => 1626870418,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_60f812abdb3b37_82187120 (Smarty_Internal_Template $_smarty_tpl) {
echo '<script'; ?>
>
const shedHeadForm = function(header,classList,funcToClose){
	if(!classList) classList = "";
	let shed = new oBaseForm2(" z105 fs12 yellowBorder op0 "+classList);
	shed.cont.style.zIndex = 1000;
	
	shed.setHeader(header);
	shed.setClose(funcToClose);
	shed.setBody();
	
	return shed;
};

const oScroller = function(){
	
	let ready = false;
	let setMouse = false;
	let directionTop = fcr("span","fs12 crPoint aUnderline");
	directionTop.textContent = "Наверх";
	directionTop.style.visibility = "hidden";
	directionTop.addEventListener("click",function(e){
		
		jContainer.scrollTop = 0;
		this.style.visibility = "hidden";
	});
	
	fget("headerWelcome").appendChild(directionTop);
	/*
	document.body.appendChild(directionTop);
	*/
	
	let jContainer = (fget("jobCont")[0]);
	
	function timeRepeater(funcItem,c,t){
		if(!t){ t = 5; }
		let tI = setInterval(function(){
			
			if(c>0){
				funcItem();
				c--;
			}else{
				clearInterval(tI);
				
				if(jContainer.scrollTop > (window.innerHeight/2)){
					/* если прокрутили достаточно низко - то покажем кнопку возвращения наверх */
					
					directionTop.style.visibility = "inherit";
				}else{
					directionTop.style.visibility = "hidden";
				}
			}
			
		},5);
		
	}
	
	/* определить мобильное ли устройство, или пк, и включать это только на мобильных утсройсвтвах
		что бы могли осуществлять навигацию нажатиями пальцев
	*/
	if(verBrowser().indexOf('Mobile')+1){
	
	
		
		jContainer.addEventListener("touchstart",function(e){
			setMouse = getPosition(e);
		});
		/*jContainer.addEventListener("mousedown",function(e){
			setMouse = getPosition(e);
		});
		jContainer.addEventListener("mouseup",function(){	
			setMouse = false;
		});*/
		jContainer.addEventListener("touchcancel",function(){
			/* когда палец вышел за пределы документа - что кажетс я само собой должно пре6рывать
			на всякий смлучай добавим, ибо может быть и при перемещении и за пределы элемента тоже нужнор так прерывать */
			setMouse = false;
		});
		jContainer.addEventListener("touchend",function(){
			setMouse = false;
		});
		

		
		
		
		jContainer.addEventListener("touchmove",function(e){
			
			if(ready){
				if(setMouse){
					/* направление бы ещё узнавать */
					
					e = getPosition(e/*,["pageX","pageY"]*/);
					/* там где мы начали это setMouse.y */
					/* там где мы остановились это e.y */
					/* только по y потому что нас интересует только прокрутка экрана вверх и вниз */
					
					if(e.y > setMouse.y){
						/* двигают зажаатый палей вниз, значит хотят вернуться наверх */
						
						timeRepeater(function(){
							jContainer.scrollTop -= ((e.y - setMouse.y)/50);
						},50);
					}else if(e.y < setMouse.y){
						
						timeRepeater(function(){
							jContainer.scrollTop += ((setMouse.y - e.y)/50);
						},50);
						
						
					}
					
					setMouse = false;
				}
			}
		});
	}
	
	$(document).keydown(function(e){
		
		if(ready){
			// console.log(e.which);
			
			if(e.which == 38){/*вверх*/
				timeRepeater(function(){
					jContainer.scrollTop -= 3;
				},40);
				
			}else if(e.which == 40){/*вниз*/
				timeRepeater(function(){
					jContainer.scrollTop += 3;
				},40);
			}
		}
		
	});
	
	
	function onWheel(e){
		if(ready){
			e = e || window.event;
			// wheelDelta не даёт возможность узнать количество пикселей
			var delta = e.deltaY || e.detail || e.wheelDelta;
			
			timeRepeater(function(){
				jContainer.scrollTop += (delta/20);
			},40);
			
		}
	}
	
	if (document.body.addEventListener) {
	  if ('onwheel' in document) {
		/* IE9+, FF17+, Ch31+ */
		document.body.addEventListener("wheel", onWheel);
	  } else if ('onmousewheel' in document) {
		/* устаревший вариант события */
		document.body.addEventListener("mousewheel", onWheel);
	  } else {
		/* Firefox < 17 */
		document.body.addEventListener("MozMousePixelScroll", onWheel);
	  }
	}
	
	
	
	this.on = function(){
		console.log("Включаем возможность прокручивания");
		ready = true;
		
		
		/* кнопка которая возвращает - прокручивает пользователя наверх страницы */
		
		/*
		directionTop.style.visibility = "inherit";
		*/
	};
	this.off = function(){
		console.log("ВЫКЛЮЧАЕМ возможность прокручивания");
		ready = false;
		
		directionTop.style.visibility = "hidden";
	}
};
const scroller = new oScroller();

const onScrollLoader = function(){
	
	let jContainer = null;
	if(jContainer = (fget("jobCont")[0])){
		
		if((jContainer.scrollHeight+41) >= document.body.scrollHeight){
			/* разрешаем обработчикам прокручивать клавиатурой и колесиком */
			scroller.on();
			
		}else{
			/* не разрешаем */
			scroller.off();
		}
		
	}else{
		/* не можем найти главный блок что ыб определить высоту его из за контента */
		throw new Error("jContainer is not found");
	}
	
};

const oMenuVume = function(c){
	let container = c;
	let listMenu = [];
	let t = this;
	
	this.isset = function(nameTab){
		for(let item of listMenu){
			if(item.btn.textContent == nameTab){
				return item;
			}
		}
		return false;
	};
	
	this.allOff = function(){
		for(let item of listMenu){
			t.offPoint(item.btn,item.bgColor);
		}
	}
	
	this.offPoint = function(point,bgColor){
		
		
		point.style.color = "#6B727E";
		point.style.borderRadius = "4px";
		point.style.border = "1px solid #707070";
		point.style.zIndex = 0;
		point.style.height = null;
		point.style.marginBottom = "10px";
		if(bgColor){ 
			point.style.background = bgColor; 
		}
	};
	
	this.onPoint = function(point){
		point.style.color = "black";
		point.style.borderRadius = "4px 4px 0 0";
		point.style.borderBottom = "aliceblue";
		point.style.zIndex = 20;
		point.style.height = "30px";
		point.style.marginBottom = null;
	};
	this.apMenu = function(btn,bgColor){
		
		if(!bgColor){ bgColor = false; }
		listMenu.push({btn:btn,bgColor:bgColor});
		btn.style.color = "#6B727E";
		btn.style.position = "relative";
		btn.style.zIndex = 0;
		
		btn.addEventListener("click",function(e){
			
			t.allOff();
			
			
			t.onPoint(this);
			
			progress.onloader = function(){
		
				onScrollLoader();
			}
			
			
		});
		
		container.ap(btn);
	}
};

const animRemoveRecord = function(record/*getParentBtn*/,funcSuccess){
	/* обычно record это родитель какой нибудь кнопки */
	let cRecord = getParent(record).childNodes.length;
	
	record.style.background = "aliceblue";
	record.style.height = (record.clientHeight-8)+"px";
	
	
	record.classList.toggle("tr02");
	record.style.background = "antiquewhite";
	record.style.color = "red";
	
	setTimeout(function(){
		record.style.height = "0px";
		record.style.opacity = 0;
		
		setTimeout(function(){
			
			record = record.remove();
			
			
			if(issetFunc(funcSuccess)){
			
				if(cRecord == 1){
					// то это была последняя запись
					cRecord = undefined;
					funcSuccess(true);
				}else{
					funcSuccess(false);
				}
				
			}
			
		},200);
		
		
	},200);
	
};

const getSpanCell = function(data,cl){
	let td = fcr("td");
	if(data && (data != "")){
		let span = fcr("span");
		span.textContent = (data+" ");
		if(cl){
			span.classList = cl;
		}
		td.appendChild(span);
	}
	return td;
}

const getTd = function(data,padding,cl){
	let td = fcr("td");
	if(data){
		td.appendChild(ftextNode(data));
	}
	if(padding){	
		td.style.padding = padding;
	}
	if(cl){
		td.classList.add(cl);
	}
	return td;
};

const testHand = function(elHand,hand,dataHand){
	
	elHand.addEventListener("click",function(e){
		if(dataHand){
			hand(e,dataHand);
		}else{
			hand(e,this.textContent);
		}
	});
	
};

/* exam
	let tableStat = testRenderTable(
		[
			{title:"Логин",key:"login",filter:true,hand:function(data){ print(data); }},
			{title:"ФИО",key:"fio",funcitem:function(td,dataItem){ customUse();  },hand:{func:function(data){ print(data); },key:"role",asbtn:"Restart" }},
			{title:"Дата регистрации",key:"date_reg",toggle:true},
			{title:"Подтверждена почта",key:"mailconfirm",format:function(resp){ return (resp?"Да":"---");  }}
		],
		statInfo.listStat
	);
	
	contStat.appendChild(tableStat.table);
*/

const testRenderTable = function(oHeaderArray,listQuery){
	/* oHeaderArray = [{.title,.key},{.title,.key},
		>>> (optional .hand => (function(.key for value) || {.keyother (as .key for value), .func(.keyother) [,.asbtn, .askey] } ) )
		>>> (optional .format => function(.key for value))
		>>> (optional .separate => (.key for value) - separe line table other bariable data)
	] */
	/* listQuery = [ .key of {} of oHeaderArray ] */
	
	let oListStatInfo = [];
	
	let table = fcr("table","sAdmT tCenter m5 w90 mWSm fs12");
	let tbody = fcr("tbody");
	let tr = null, td = null, el = null;
	
	let count = listQuery.length;
	let n = 1, resp = null, btn = null, tmp = null, br = false, t = null;
	let separate = false;
	let newList= [];
	let utime = false, nextUp = false;
	let toggle = 0, newThemeTr = false;
	let idDown = false;
	
	const testKeydownFilter = function(t,listQuery,key){
	
		if(t.value.length > 2){
			newList = [];
			
			listQuery.forEach(function(item){
				
				resp = eval("item."+(key));
				
				if(resp && (resp != "") && fs(resp.toUpperCase(),t.value.toUpperCase())){
					newList.push(item);
				}
				
			});
			
		}else{
			newList = listQuery;
		}
		
		completeRemove(t,listQuery,key);
	};
	
	
	function completeRemove(t,listQuery,key){
		
		animRemoveRecord(tbody,function(last){
			
			/* это тут обязательно */
			/* если просто tbody = undefined; - то не помогает */
			tbody = tbody.remove();
			/* потому что в animRemoveRecord .remove() будто был над копией */
			
			tbody = fcr("tbody"); 
			table.appendChild(tbody); 
			
			n = 1;
			render(newList);
			
			
			if(nextUp){
				newList = [];
				/* то выполнить операцию повторно, ибо добавились нажатия */
				testKeydownFilter(t,listQuery,key);
				
				nextUp = false;
				utime = false;
				
			}else{utime = false;}
			
		});
		
	}
	
	function testSort(list,field,downDirection){
	
		list = list.sort(function(a,b){
			
			a = eval("a."+field);
			b = eval("b."+field);
			
			if(field == "id"){ 
				a = parseInt(a);
				b = parseInt(b);
			}
			
			if(a < b){ return (downDirection?1:-1); }
			if(a > b){ return (downDirection?-1:1); }
			
			return 0;
		});
		
		
		tbody = tbody.remove();
		tbody = fcr("tbody"); 
		table.appendChild(tbody);
		
		
		render(list);
	}
	function startSort(field,downDirection){
		/* если была применена фильтрация, то сортируем newList */
		/* иначе listQuery */
		
		if(field == "id"){
			if(downDirection){
				idDown = true;
				n--;
			}else{
				idDown = false;
				n++;
			}
			
		}
		
		if(newList.length > 0){
			testSort(newList,field,downDirection);
		}else{
			testSort(listQuery,field,downDirection);
		}
	}
	
	if(listQuery.length > 0){
		let thead = fcr("thead");
		
		tr = fcr("tr","p10");
		
		td = getSpanCell("id","crPoint m2 ");
		td.style.width = "32px";
		td.addEventListener("click",function(e){
			/* если у его родителя есть ребёнок с icSortDown */
			/* то сортируем в обратном порядке - наверх, и меняем иконку того класса на */
			/* icSortUp */
			
			e = getParent(e.target);
			
			if(el = e.querySelector('.icSortDown')){
				el.classList.toggle('icSortDown');
				el.classList.toggle('icSortUp');
				
				startSort("id",0);
				
			}else if(el = e.querySelector('.icSortUp')){
				el.classList.toggle('icSortDown');
				el.classList.toggle('icSortUp');
				startSort("id",1);
			}
			
			
			
			/* и, наоборот */
			
		});
		
		
		el = fcr("div","dInlBl left icSortUp");
		td.appendChild(el);
		
		/* css icSortUp icSortDown */
		/*
		td.appendChild(getBtn("Down",function(e,btn){
			
			
			
		}));*/
		
		tr.appendChild(td);
		
		
		
		for(let i of oHeaderArray){
			td = getSpanCell(i.title?i.title:"","crPoint");
			
			if(i.filter){
				tmp = fcr("input","fs12 itemFilterHead");
				tmp.type = "text";
				
				tmp.size = 7;
				tmp.addEventListener("keyup",function(e){
					t = this;
					if(!utime){
						
						utime = true;
						/* все кроме текущего фитрта - очистим, потому что пока может применять */
						/* только один фильтр в одно время */
						newList = fgetParent(fgetParent(this)).querySelectorAll('.itemFilterHead');
						newList.forEach(function(item){
							if(item != t){ item.value = ""; }
						});
						
						/* так же сортировку тоже пока сбрасываем */
						idDown = false;
						n=1;
						newList = fgetParent(fgetParent(this)).querySelectorAll('.icSortDown');
						newList.forEach(function(item){
							item.classList.toggle('icSortDown');
							item.classList.toggle('icSortUp');
						});
						
						testKeydownFilter(t,listQuery,i.key);
						
					}else{
						nextUp = true;
					}
					
				});
				td.appendChild(tmp);
			}
			
			tr.appendChild(td);
			if(i.separate){ separate = i.key; }
		}
		
		tr.style.background = "aquamarine";
		thead.appendChild(tr);
		table.appendChild(thead);
	}
	
	function render(listItems){
		
		for(let i in listItems){

			el = listItems[i];
				
			tr = fcr("tr");
			tbody.ap = tr.ap = tr.appendChild;
			
			td = fcr("td");
			td.appendChild(ftextNode(n)); 
			if(idDown){ n--; 
			}else{ n++; }
			
			tr.ap(td);
			
			
			for(let h of oHeaderArray){
				
				if(h.toggle){
					if(newThemeTr && (eval("el."+h.key) != newThemeTr)){ toggle++; }
					newThemeTr = eval("el."+h.key);
					
					if(toggle % 2){ tr.style.background = "aquamarine"; }
				}
				
				
				if(h.key){ 
					resp = eval("el."+(h.key)); 
					if(h.format && (issetFunc(h.format))){
						
						resp = h.format(resp);
					}
					br = true;
				}else{ resp = false; br = false; }
				
				
				
				if(h.hand){
					btn = null;
					if(!h.hand.asbtn){
						resp = getSpanCell(resp,"aUnderline crPoint");
					}else{
						resp = getSpanCell(resp);
						resp.classList.add("tLeft");
						btn = getBtn(h.hand.asbtn,function(){
							
						});
						if(br){ resp.appendChild(fcr("br")); }
						resp.appendChild(btn);
						
						/* теперь resp не false - если был ей */
					}
					
					
					
					if(issetFunc(h.hand)){
						if(!h.key){ throw new Error("h.key is not found. not object to addEventListener"); }
						resp.addEventListener("click",function(e){
							h.hand(this.textContent);
						});
					}else if(typeof(h.hand) == "object"){
						
						if(h.hand.asbtn){
							
							
						}
						
						
						/* .key .func */
						if(h.hand.func && issetFunc(h.hand.func)){
							testHand((btn?btn:resp),h.hand.func,(h.hand.key?eval("el."+(h.hand.key)):el));
						}
						
						
					}
				
				}else{
					resp = getSpanCell(resp);
				}
				
				if(h.funcitem && issetFunc(h.funcitem)){
					h.funcitem(resp/*td*/,((h.hand && h.hand.key)?eval("el."+h.hand.key):el),i,tr);
					
				}
				
				tr.ap(resp);
			}
			
			tmp = {tr:tr,data:el};
			oListStatInfo.push(tmp);
			
			tbody.ap(tr);
		}
	}
	
	render(listQuery);
	table.appendChild(tbody);
	return {table:table,oListStatInfo:oListStatInfo};
};

const splitText = function(text,c){
	
	let str = "";
	if((text.length > c) && (text != " ")){
		
		if((fs(text," ") > c) || (!fs(text," "))){
			
			if(fs(text," ") > c){
				/* вдруг следующий символ точка или одна буква перед точкой.
				То уж лучше не разделять */
				
				if(fs(text," ") > (c+2)){
					
					str = (text.substring(0,c)+' '+splitText(text.substring(c),c));
				}else{
					
					str = (text.substring(0,(c+2))+' '+splitText(text.substring(c+2),c));
				}
				
			}else{
				
				if(text.length > (c+2)){
					
					str = (text.substring(0,c)+' '+splitText(text.substring(c),c));	
				}else{
					
					str = text;
				}
			}
			
			
		}else if(fs(text," ") < c){
			
			str = (text.substring(0,fs(text," "))+' '+splitText(text.substring(fs(text," ")),c));
		}else{
			/* при равенстве позиции пробела числу c */
			str = (text.substring(0,fs(text," "))+splitText(text.substring(fs(text," ")),c));
		}
		
		
	}else{ str = text; }
	
	str = str_replace("  "," ",str);
	return str;
};

const getBtnCatFolder = function(classIcon,textFolder,hand,gorizontal){
	let folder = fcr("div","dInlBl m5");
	
	/* icFolder */
	let icFolder = fcr("div","crPoint posRel "+classIcon);
	if(gorizontal){ folder.classList.add(gorizontal); }
	folder.appendChild(icFolder);
	icFolder.addEventListener("click",function(e){
		try{
			if(progress){
				if(gorizontal){
					
					progress.log("click() "+classIcon+": run test '"+textFolder+"'");
					
				}else{
					/*
					progress.log("click() "+classIcon+": move folder '"+textFolder+"'");
					*/
					progress.log("click() "+classIcon+": move folder '"+getParent(this).textContent+"'");
				}
			}
			hand(e,fgetParent(e.target));
		}catch(ex){
			printLog(ex);
		}
	});
	
	folder.ap = function(child){
		this.appendChild(child);
	}
	
	if(!gorizontal){
		/* если вертикальная схема */
		folder.style.width = "60px";
		folder.style.margin = "5px 2px 2px 5px";
		// folder.style.height = "72px";
	}else{
		icFolder.classList.add("left");
	}
	
	let text = fcr("text","left "+(gorizontal?"txtCenter":""));
	text.style.width = "60px";
	
	if(!gorizontal){
		text.style.width = "76px";
		text.style.marginLeft = "-7px";
		text.classList.add("txtCenter");
		
		let c = 16;
		let countLR = 9;
		
		text.textContent = splitText(textFolder.substring(0,c),countLR);
		if(textFolder.length > c){
			text.textContent += '..';
		}
		text.turn = true;
	
	
	
	
		text.addEventListener("mouseup",function(){
			if(this.turn){
				
				/* если ближайший пробел встречается раньше чем c символов */
				/* то разорвем первое слово пробелом, что бы сразу переносилось на новую строку */
				this.textContent = splitText(textFolder,countLR);
				
				this.turn = false;
			}else{
				/* но только если не для выделение текста!! */
				if(getSelectedText().isCollapsed){
					
					this.textContent = splitText(textFolder.substring(0,c),countLR);
					
					if(textFolder.length > c){
						this.textContent += '..';
					}
					this.turn = true;
				}else{
					/* выделили текст, так может сразу копировать его в буфер */
					fCopyToBuffer(textFolder);
					fliteComplete(false,"Помещено в буфер");
					
				}
			}
		});
		
	
		fhelper(text,textFolder);
	}else{
		if((fs(textFolder," ") > 11) || (textFolder.length > 11)){
			text.textContent = textFolder.substring(0,11)+' '+textFolder.substring(11);
		}else{
			text.textContent = textFolder;
		}
		
		
		
		text.addEventListener("mouseup",function(){
			
			/* для выделяемого текста */
			if(!getSelectedText().isCollapsed){
				
				fCopyToBuffer(textFolder);
				fliteComplete(false,"Помещено в буфер");
				
			}
		});
		
	}
	
	folder.appendChild(text);
	return folder;
};



/* exam:
listQuery = JSON.parse(listQuery).listQuery;
listQuery = compareQuery(listQuery);

formAsMultitabsTable(
	listQuery,
	{default:"Справочники",current:itemTest.theme},
	buildTableSp,
	"formAsMultitabsTable"
);
*/
/* buildTableSp(listQuery); */

const formAsMultitabsTable = function(listItems,titleform,tableBuilder,idform){
	/* titleform = {.default:,.current:} */
	
	let form = null;
	
	
	if(idform && (form = fget(idform))){
		/* // если при наличии id нужно просто заменять форму
		form.remove();
		*/
		
		/* если нужно добавлять на форму новую вкладку */
		if(form.apptab){
			/* если есть такая функция и форма найдена, то воспользуемся */
			
			let crSectContainer = fget("crSectContainer")[0];
			let lastTab = null;
			let dy = null;
			
			/* form.listData */
			if(!form.passtab){
				/* то переведём изначальный контент на вкладку */
				
				form.passtab = crSectContainer.innerHTML;
				
				lastTab = getBtn(form.listData.current,function(e,btn){
				
					
				
					crSectContainer.clear();
					
					crSectContainer.innerHTML = form.passtab;
					
				},"tr02");
				
				lastTab.addEventListener("click",function(e){
					dy = (findPosY(crSectContainer) - (findPosY(e.target) + e.target.scrollHeight));
					if(dy > 16){
						/* то отменяем особенное выделение в виде удлиннения */
						/* так как между рядом этим, помещён похоже ещё один ряд кнопок */
						
						
						setTimeout(function(){
							e.target.style.height = null;
							e.target.style.borderRadius = "4px";
							e.target.style.border = "1px solid #707070";
							e.target.style.marginBottom = "9px";
						},20);
						
					}
				});
				
				
				/*
				lastTab.style.maxWidth = "85px";
				lastTab.style.maxHeight = "30px";
				*/
				form.apptab(lastTab);
				/* а заголовок формы переименуем в listData.default */
				
				crSectContainer.style.borderTop = "1px solid gray";
				crSectContainer.style.marginTop = "-11px";
				crSectContainer.style.padding = "0px";
				crSectContainer.style.paddingTop = "10px";
				
				form.firstChild.firstChild.firstChild.firstChild.textContent = form.listData.default;
			}
			
			
			if(!(lastTab = form.isset(titleform.current))){
			
				lastTab = getBtn(titleform.current,function(e,btn){
					
					
					crSectContainer.clear();
					
					tableBuilder(listItems,crSectContainer);
					
				},"tr02");
				
				lastTab.addEventListener("click",function(e){
					dy = (findPosY(crSectContainer) - (findPosY(e.target) + e.target.scrollHeight));
					if(dy > 16){
						/* то отменяем особенное выделение в виде удлиннения */
						/* так как между рядом этим, помещён похоже ещё один ряд кнопок */
						
						
						setTimeout(function(){
							e.target.style.height = null;
							e.target.style.borderRadius = "4px";
							e.target.style.border = "1px solid #707070";
							e.target.style.marginBottom = "9px";
						},20);
						
					}
				});
				
				
				/*
				lastTab.style.maxWidth = "85px";
				lastTab.style.maxHeight = "30px";
				*/
				fhelper(lastTab,titleform.current);
				form.apptab(lastTab);
			
			}
			
			lastTab.click();
			
		}
		
		
	}else{
	
	
	
	
		form = shedForm(false,titleform.current,
			function(f,block){
				/* funcInit */
				
				let blockItem = f.crSectButton("crSectContainer");
				let blockHead = f.crSectButton();
				
				f.cont.style.width = "500px";
				
				let tabCont = new oMenuVume(blockHead);
				f.cont.isset = tabCont.isset;
				f.cont.apptab = tabCont.apMenu;
				
				tableBuilder(listItems,blockItem,f.cont);
				
				block.appendChild(blockHead);
				block.appendChild(blockItem);
				
				/*
				f.cont.style.height = "618px";
				block.style.maxHeight = "552px";
				// 682
				*/
			}
		);
		
		if(idform){
			form.cont.id = idform;
			/* просто что бы можно было снаружи добавлять вкладки */
			
			form.cont.listData = titleform;
			
			
			
			
			return form;
		}
	
	}
};

const getLinkOnProfile = function(qmodule,elLogin,cl){
	
	let tmp = fcr("span","m5 aUnderline crPoint "+cl);
	tmp.textContent = elLogin.login;
	
	tmp.addEventListener("click",function(e){
		
		cache.store("click() profile.link "+elLogin.login);
		
		qmodule.onErrorput.getUserData(function(userData){
			
			userData = userData.data;
			
			formPublicDataUser(e,userData);
			
		},elLogin.login);
		
		
	},false);
	
	return tmp;
};

const getFormInfo = function(e,staticInfo,func){
	
	shedForm(e,"Детали ",function(form,blockToCont){
						
		/* сюда передано readCategories2 как funcStart */
		// funcStart("Корень категорий",0,blockToCont,0);
		
		blockToCont.style.minHeight = "28px";
		blockToCont.style.border = "1px solid rgb(112, 112, 112)";
		blockToCont.classList.add("bgWhite");
		blockToCont.classList.add("r3");
			
		blockToCont.appendChild(staticInfo);
		if(issetFunc(func)){
			func(form,blockToCont);
		}
		
	});
	
};

/*
	formMenu = testFormMenu(e,
					function(block){
						let btn = fcr("div","crPoint posRel closeMin noR right");
						btn.addEventListener("click",function(){
							block = block.remove();
						});
						
						block.appendChild(btn);
						block.appendChild(fcr("br"));
						
				btn = getBtn("Профиль",function(){
				
				apMenu.allOff();
				formMenu = formMenu.remove();
				
	...
*/

const testFormMenu = function(e,func,id){
	
		let blockToCont = fcr("div","op0 z55 newShadow fs17 tr02 p5 r5 bgWhite");
		blockToCont.style.minHeight = "28px";
		blockToCont.style.width = "100px";
		blockToCont.style.border = "1px solid rgb(112, 112, 112)";
	
		if(issetFunc(func)){ func(blockToCont); }
	
		fpoliticRePos(e,blockToCont,id);
		return blockToCont;
};

const formPublicDataUser = function(e,dataUser){
	
	shedForm(e,"Данные профиля "+dataUser.login,function(form,blockToCont){
						
		/* сюда передано readCategories2 как funcStart */
		// funcStart("Корень категорий",0,blockToCont,0);
		
		blockToCont.style.border = "1px solid rgb(112, 112, 112)";
		blockToCont.classList.add("bgWhite");
		blockToCont.classList.add("r3");
			
			
		blockToCont.appendChild(ftextNode(dataUser.fio?dataUser.fio:(dataUser.last_name+" "+dataUser.first_name+" "+dataUser.patronymic)));
		if(dataUser.date_reg){
			blockToCont.appendChild(fcr("br"));
			blockToCont.appendChild(ftextNode("Дата регистрации "+fconvertToRusDate(dataUser.date_reg)));
		}
		if(dataUser.mailconfirm){
			blockToCont.appendChild(fcr("br"));
			blockToCont.appendChild(ftextNode("Почта "+((dataUser.mailconfirm == 1)?"":"НЕ ")+"подтверждена"));
		}
		blockToCont.appendChild(fcr("br"));
		
		
		if(dataUser.priority && dataUser.role){
			blockToCont.appendChild(fcr("br"));
			blockToCont.appendChild(ftextNode("Приоритет = "+dataUser.priority+"; "));
			blockToCont.appendChild(ftextNode("Идентификатор = "+dataUser.role));
			
			if(dataUser.email){
				blockToCont.appendChild(fcr("br"));
				blockToCont.appendChild(ftextNode("email = "));
				
				let spanLink = fcr("span","aUnderline crPoint");
				spanLink.textContent = dataUser.email;
				blockToCont.appendChild(spanLink);
			}
			blockToCont.appendChild(fcr("br"));
			blockToCont.appendChild(fcr("br"));
		}
	});
};


/* этот пока только для логов */
const cache = new function(){
	this.store = function(mess){
		if(progress && progress.log){
			progress.log(mess);
		}
	}
};
/* просто ответственный за офррмление при загрузке */
const oProgressBar = function(timeoutSec/* на слабых устройствах выставлять его побольше */){
	/*а после загрузки чего либо, мы должны получить элемент progressBar,
	и вызвать у него .onclick следующим образом = fget("progressBar")[0].onclick();*/
	
	console.log("new oProgressBar");
	
	if(!timeoutSec) timeoutSec = 20000;
	else timeoutSec *= 1000;
	
	let tForm = null;
	let t = this;
	let blockScreen = null;
	this.onloader = null;
	this.listProcess = [];
	
	this.checkRecord = false;
	this.listStore = [];
	this.onloaderSecond = [];
	
	this.logger = function(mess){
		if(typeof(mess) == "string"){
			console.log(mess);
			if(this.checkRecord){
				this.listStore.push(mess);
			}
		}else if(is_array(mess)){
			mess = Array.from(mess);
			for(let item of mess){
				this.logger(item);
			}
		}
	}
	this.log = this.logger;
	
	this.start = function(mess/*,willNotServerLoad*/){
		/* willNotServerLoad - означает что не будет запроса на сервер, значит не будет задержки */
		
		if(!mess){ mess = "start was call"; }
		this.logger(mess);
		
		tForm = fcr("div","posAbs z155 progressBar");
		
		tForm.style.backgroundSize="contain";
		tForm.style.width=158+"px";
		tForm.style.height=24+"px";
		
		fsetPos(tForm, fgetCenterX(tForm), fgetCenterY(tForm));
		fdocAp(tForm);
		
		if(!blockScreen){
			blockScreen = fcr("div", "posFix blockscreen z45");
			fdocAp(blockScreen);
		}
		
		if(!t.interval){
			t.interval = setInterval(function(){
				
				fliteComplete(t,"Похоже загрузки мы не дождёмся");
			},timeoutSec);
		}
		
		t.listProcess.push(tForm);
	}
	
	this.close = function(mess){
		
		setTimeout(function(){
			
			if(t.listProcess.length == 1){
				/* когда последний процесс обмена данными */
				clearInterval(t.interval);
				fRemove(blockScreen);
				blockScreen = null;
			}
			
			if(!mess){ mess = "close was call"; }
			if(t.onloader){
				t.onloader();
				t.onloader = null;
			}
			if((is_array(t.onloaderSecond)) && (t.onloaderSecond.length > 0)){
				/* потому что выполнять тестирование интерфейса нельзя используя .onloader */
				
				/*
				(t.onloaderSecond.shift())();
				*/
				setTimeout(function(){
					if(t.onloaderSecond.length > 0){
						(t.onloaderSecond.shift())();
					}
				},1000);
				
			}
			
			t.logger(mess);
			
			let count = t.listProcess.pop();
			
			// console.log(count);
			
			fRemove(count);
			
		},70);
		
	}
};
	
const listCombFontFamily = function(val,func,listFamily){
	/* arrVal - listFamily */
	
	if(!listFamily){
		fliteComplete(false,"Не обнаружены семейства шрифтов");
	}else{
		let cmBox = listCombobox();
		cmBox.classList.add("fs12");
		let el = null;
		for(let i in listFamily){
			if(val && (val == listFamily[i])){ el = fgetOption(listFamily[i],listFamily[i],val);
			}else{ el = fgetOption(listFamily[i],listFamily[i]); }
			
			if(func){
				el.onclick = function(){
					fhelper(cmBox,this.value);
					
					if(typeof(func)=="function"){ func(this.value); }
				}
			}
			cmBox.appendChild(el);
		}
		
		fhelper(cmBox,cmBox.value);
		
		cmBox.style.width = "100%";
		fsetAp(cmBox);
		return cmBox;
	}
	return false;
}

const listCombFontSize = function(val,func,arrVal){
	let cmBox = listCombobox();
	cmBox.classList.add("fs12");
	
	let el = null, startVal = 12, topVal = 35, h = 2;
	
	if(arrVal && (typeof(arrVal) == "object") && (arrVal.length>0)){
		startVal = arrVal[0];
		
		if(arrVal.length > 1){
			topVal = arrVal[arrVal.length-1];
			h = (arrVal[1] - arrVal[0]);
		}
	}
		for(let i = startVal; i<topVal; i+=h){
			
			if(val && ((val == i) || ((i<=(topVal-h)) && (val >= i) && (val <= (i+h))) || ((i>=h) && (val >= (i-h)) && (val <= i)) )){
				el = fgetOption(val,i,val);
			}else{
				el = fgetOption(i,i);
			}
			
			if(func){
				el.onclick = function(){
					fhelper(cmBox,this.value);
					if(typeof(func)=="function"){ func(this.value); }
				}
			}
			cmBox.appendChild(el);
		}
	
	cmBox.onchange = function(){
		fliteComplete(false,"cmBox.onchange");
		func(this.value);
	}
	
	fhelper(cmBox,cmBox.value);
	return cmBox;
}
	
const fromFormToBase = function(v){
	v = str_replace("[","={{",v);
	v = str_replace("]","}}=",v);
	
	v = str_replace("(","={",v);
	v = str_replace(")","}=",v);
	return v;
}
	
const fromBaseToForm = function(def){
	def = str_replace("={{","[",def);
	def = str_replace("}}=","]",def);
	
	def = str_replace("={","(",def);
	def = str_replace("}=",")",def);
	return def;
}

const shedForm = function(e,oHeader,func,funcToClose){
	
	let cl = "";
	if(!e){ cl = " centerForm "; }
	let righttoNewForm = shedHeadForm(oHeader.title,cl,funcToClose);
	
	let block = righttoNewForm.crSectButton();
	
	righttoNewForm.cont.style.width = oHeader.w+'px';
	if(func) func(righttoNewForm,block);
	
	block.style.overflowY = "scroll";
	block.style.overflowX = "hidden";
	block.style.height = 100 + "%";
	block.style.maxHeight = oHeader.h + "px";
	/* block.maxH(1700); */
	// block.style.maxHeight = (document.body.scrollHeight-300)+'px';
	
	
	icon.turn(righttoNewForm,block);
	
	
	fpoliticRePos(e,righttoNewForm.cont);
	
	righttoNewForm.cont.style.height = "";
	righttoNewForm.bodyForm.style.maxHeight = oHeader.h+'px';
	
	
	righttoNewForm.eResize = new oResize(righttoNewForm.cont,(oHeader.w?(oHeader.w/3):270),block);
	
	return righttoNewForm;
};

const getBtn = function(text,func,classAp,messhelper,clickOnlyOne){
	try{
		if(!classAp) classAp = "";
		let btn = fbtn2(text, "m2 noSelect "+classAp);
		btn.staticCoor = false;
		btn.addEventListener("click",function(e){
			try{
				if(this.active && (!this.clicked)){
					toolTip();
					if(clickOnlyOne) this.clicked = true;
					
					
					if(progress){
						cache.store("click() btn '"+this.textContent+"'");
					}
					
					if(issetFunc(func)) func(e,this);
				}
			}catch(ex){ printLog(ex); }
		},false);
		btn.addEventListener("testClick",function(e){
			console.log("I am btn testClick");
			this.click();
		});
		
		if(messhelper) fhelper(btn,messhelper);
		return btn;
		
	}catch(ex){throw ex;}
};

const getBtnForm = function(text,func,classAp,messhelper,clickOnlyOne){
	try{
		/* если кнопки на форму, то пусть они будут чуть меньше если это запущено на ПК */
		
		if(verBrowser() != "Mobile"){
			classAp += " fs12";
		}
		
		return getBtn(text,func,classAp,messhelper,clickOnlyOne);
	}catch(ex){throw ex;}
};

const getBtnEditHand = function(text,func,classAp,messhelper,clickOnlyOne){
	try{
		if(!classAp) classAp = "";
		let btn = fbtn2(text, "m2 noSelect "+classAp);
		btn.staticCoor = false;
		btn.onclick = function(e){
			try{
				if(this.active && (!this.clicked)){
					toolTip();
					if(clickOnlyOne) this.clicked = true;
					if(issetFunc(func)) func(e,this);
				}
			}catch(ex){ printLog(ex); }
		}
		if(messhelper) fhelper(btn,messhelper);
		return btn;
		
	}catch(ex){throw ex;}
}

const getCountPoint = function(val,mess){
	let el = getCont("btnDef countpoint right");
	if(val) el.textContent = val;
	if(mess) fhelper(el,mess);
	return el;
}

const reArrayOfObject = function(arr){
	let n = null;
	arr.clear = function(){
		for(let i=(this.length-1); i>=0; i--){
			this.pop();
		}
	}
	arr.remove = function(n){
		/* console.log("удаляемая позиция = "+n); */
		let r = [];
		for(let i=0; i<this.length; i++){
			if(i != n) r.push(this[i]);
		}
		n = null;

		/* оценить алгоритмы по быстродействию - */
		/* возможно стоит найти элемент, разобрать массив, и склеить без него */
		
		r.clear = this.clear;
		r.remove = this.remove;
		r.indexRemove = this.indexRemove;
		r.actual = this.actual;
		r.customIndexOf = this.customIndexOf;
		return r;
	}
	arr.indexRemove = function(id){
		
		for(let i=0; i<this.length; i++){
			if(this[i].id == id) return this.remove(i);
		}
		
		// if(n = (this.indexOf(val)+1)) return this.remove(--n);
		return false;
	}
	arr.customIndexOf = function(id){
		/* console.log("arr.indexOf. id = "+id);
		console.log(this); */
		
		for(let i=0; i<this.length; i++){
			if(this[i].id == id) return true;
		}
		return false;
	}
	/* удаляет если элемент уже есть, и вносит если его нет */
	arr.actual = function(obj){
		
		if(n = this.indexRemove(obj.id)){
			/* console.log("элемент удалён - и вернули новый массив"); */
			return n;
		}
		
		this.push(obj);
		/* элемент добавлен */
		return this;
	}
}

const fBoldNode = function(text, cl){
	let nodeNode = document.createTextNode(text);
	let textNode = fcr("text",cl);
	textNode.appendChild(nodeNode);
	textNode.style.fontWeight = 600;
	return textNode;
}

const getCont = function(cl){
	return fd("p5 posRel "+cl);
	/* .ap(other el) .ini(func(el)) .clear() .cl(e,el) */
}

const getParentAbs = function(el){
	// например для поиска offsetTop и offsetLeft
	
	if(el && el.classList){
		if(el.classList.contains("posAbs")) return el;
		else return getParentAbs(fgetParent(el));
	}
	return false;
}

const getCheckbox = function(nid,hand,cl){
			
	let ch = fcr("input",(cl?cl:""));
	ch.type = "checkbox";
	
	if(typeof(hand) == "function"){
		ch.onclick = function(){
			hand(nid,this);
		}
	}
	return ch;
}

const testBlockTextAndActive = function(text){
	let contStr = getCont("left w95");
	contStr.ap(ftextNode(text));
	
	let rBlock = fd();
	contStr.rBlock = rBlock;
	contStr.ap(rBlock);
	
	contStr.ins = function(view,type){
		inp = fcr(view);
		if(type) inp.type = type;
		contStr.inp = inp;
		rBlock.ap(inp);
	}
	
	return contStr;
}

const getStrAndInput = function(text){
			
	/* для надписи и инпута */
	let contStr = getCont("p5 left w100");
	contStr.ap(ftextNode(text));
	contStr.ap(document.createElement("br"));
	let inp = null;
	contStr.ins = function(view,type,name){
		inp = fcr(view,"w100");
		if(type) inp.type = type;
		if(name) eval("contStr."+name+"="+name);
		contStr.inp = inp;
		contStr.ap(inp);
	}
	
	return contStr;
}

/* не нужна так как есть .map(item => {}) */
const iterator = function(arrayList,func){
	
	if(func && (typeof(func)=="function")){
		if((typeof(arrayList) == "object") && (arrayList.length>0)){
			
			for(let i=0; i<arrayList.length; i++){
				func(arrayList[i]);
			}
			
		}else func(false);
	}
}

const listCombobox = function(){
	return fcr("select", "btnDef crPoint gradient dInlBl crPoint");	
}


const toInt = function(val){
	let end = "";
	let el = null;
	for(let i=0; i<val.length; i++){
		
		if((el = parseInt(val[i])) >= 0) end += el;
	}
	return end;
}
/* перепроверить */
function keyInt(vinp,maxInt,minInt){
	if(parseInt(toInt(vinp.value)) > (minInt?minInt:0)){
		if(maxInt && (parseInt(toInt(vinp.value))>maxInt)){
			vinp.value = maxInt;
		}else{
			vinp.value = parseInt(toInt(vinp.value));
		}
	}else{
		if(minInt || (minInt === 0)) vinp.value = (minInt);
		else vinp.value = 1;
	}
}

function blurInt(vinp){
	vinp.addEventListener('blur',function(){
		if(this.value.length < 1){
			this.value = 1;
		}
	});
}



const crPlaceOfSpaceHeader = function(naem,cl){
	return fcr(naem, "btnDef dInlBl m2 "+cl);
}

/* test active panel */

const getMoverBtn = function(text,func){
	let btn = getBtn(text,func,"posAbs newShadow MoverBtn noSelect");
	btn.style.top = "50px";
	btn.style.left = "50px";
	fsetMove(btn);
	
	return btn;
}

const steaper = function(el){
	let step = 20;
	el.move = function(x,y){
		if(!this.classList.contains("tr02")) this.classList.add("tr02");
		this.position(
			(x && (((x>0) && (x<=1)) || ((x<0) && (x>=-1))))?((x*step)+parseInt(this.style.left)):false,
			(y && (((y>0) && (y<=1)) || ((y<0) && (y>=-1))))?((y*step)+parseInt(this.style.top)):false
		);
		let t = this;
		setTimeout(function(){t.classList.remove("tr02");},200);
		
	}
	el.position = function(x,y){
		if(y) this.style.top = y+"px";
		if(x) this.style.left = x+"px";
	}
}

const freeBtn = function(header,func){
	
	function removePlace(e,btn){
		let newe = getPosition(e);
		let parentAbs = null;
		
		if(!btn.setting.active){
			btn.setting.active = true;
			
			btn.style.top = newe.y-(btn.clientHeight/2)//-e.layerY;
			btn.style.left = newe.x-(btn.clientWidth/2)//-e.layerX;
			
			if(!btn.classList.contains("posAbs")) btn.classList.add("posAbs");
			if(btn.classList.contains("left")) btn.classList.remove("left");
			
			if(btn.classList.contains("crPoint")){
				btn.classList.remove("crPoint");
				btn.classList.add("crMove");
			}
			
			setTimeout(function(){
				
				thisTank.href = btn;
				move.fmoveDown(e, btn);
				
			},30);
			
			// fdocAp(btn);
			placeTests.ap(btn);
			
			btn.style.zIndex++;
		}
	}
	
	let bt = getMoverBtn(header,function(e,btn){
		/* .onmouseup, он же .click */
		removePlace(e,btn);
		
		// console.log(e);
		
		if(issetFunc(func)) func(e,btn);
	});
	
	bt.addEventListener("mousedown",function(e){
		
		// console.log(this);
		removePlace(e,this);
	},false);
	
	steaper(bt);
	return bt;
}
/*
fdocAp(freeBtn("btn 1"));
*/
// interactivePanel("header")
const interactivePanel = function(header,funcA){
	let el = null;
	let i = 0;
	
	
	if(!header){
		el = crPlaceOfSpaceHeader("div","posAbs newShadow");
		el.style.marginTop = "22px";
		el.style.minHeight = "52px";
	}
	else {
		el = interactivePanel();
		let head = crPlaceOfSpaceHeader("text","left");
		head.style.marginTop = "-22px";
		head.textContent = header;
		el.appendChild(head);
		el.appendChild(document.createElement("br"));
		
		
		let cont = fcr("div", "m2 left r3 bgWhite crDef yellowBorder");
		
		fnomove(cont);
		
		cont.style.height = "52px";
		cont.style.minHeight = "52px";
		cont.style.marginBottom = "-2px";
		cont.style.marginLeft = "-10px";
		cont.style.border = "1px solid rgb(112, 112, 112)";
		
		
		fsetAp(cont);
		
		cont.addEventListener('mousemove', function(e){
			
			let tankTop = null;
			let thisTop = null;
			
			let tankLeft = null;
			let thisLeft = null;
			if(thisTank && thisTank.href && (thisTank.href.classList.contains("MoverBtn"))){
				/* но нужно узнать его дистанцию */
				tankTop = parseInt(thisTank.href.style.top);
				tankLeft = parseInt(thisTank.href.style.left);
				
				thisTop = this.offsetParent.offsetTop+this.offsetTop;
				thisLeft = this.offsetParent.offsetLeft+this.offsetLeft;
				
				
				if((tankTop>=thisTop) && ((tankTop+(parseInt(thisTank.href.clientHeight)))<=(thisTop+(parseInt(this.clientHeight))))){
					if((tankLeft>=thisLeft) && ((tankLeft+(parseInt(thisTank.href.clientWidth)))<=(thisLeft+(parseInt(this.clientWidth))))){
					
				
					
						this.ap(thisTank);
						thisTank = {};
						
						if(issetFunc(funcA)) funcA(this);
					}// else fliteComplete(false,"неверно по горизонтали");
					
				}// else fliteComplete(false,"неверно по высоте");
				
			}
			
		}, false);
		
		cont.ap = function(btn){
			if(btn){
				if(btn.href) btn = btn.href;
				btn.style.top = null;
				btn.style.left = null;
				if(btn.classList.contains("posAbs")) btn.classList.remove("posAbs");
				if(!btn.classList.contains("left")) btn.classList.add("left");
				if(btn.classList.contains("crMove")){
					btn.classList.remove("crMove");
					btn.classList.add("crPoint");
				}
			
				btn.setting.active = false;
				this.appendChild(btn);
			}
		}
		
		
		steaper(el);
		el.loaded = function(){
			
			//cont.style.minWidth = head.clientWidth+"px";
			cont.style.minWidth = "102%";
			el.appendChild(new oResize(cont,(head.clientWidth)).rR);
			
			//this.eResize = new oResize(this,head.clientWidth,cont);
			
		}
		el.appendChild(cont);
		el.position(50,50);
		fsetMove(el);
	}
	
	/* после вставки элемента позвать его .loaded() */
	return el;
}

const handActivePanel = function(header,func){
	/* когда каждая из кнопок помещается на панель */
	return interactivePanel(header,function(container){
		if(container.childNodes){
			let сomplete = "";
			
			for(let i=0; i<container.childNodes.length; i++){
				сomplete += container.childNodes[i].textContent;
				if(i<(container.childNodes.length-1)) сomplete += " ";
			}
			
			if(issetFunc(func)){ func(сomplete,container.childNodes[container.childNodes.length-1].textContent);
			}else fliteComplete(false,сomplete);
		}
	});
}



const testerActivePanel = function(place,wordsCompare,headTicketMess){
	// "MoverBtn"
	let j=0;
	let cword = null;
	let interval = null;
	let contentWords = false;
	
	function nextWord(){
		// debugger;
		if(j>(wordsCompare.length-1)) j=0;
		
		if((contentWords) && (cword = wordsCompare[j++])){
			
			if(fs(contentWords,cword)) return nextWord();
			else return cword;
		}
		return wordsCompare[j++];
	}
	
	function moverBtn(classMoveEl,funcMove){
		classMoveEl = fget(classMoveEl);
		if(classMoveEl.length>0){
			for(let i=0; i<classMoveEl.length; i++){
				if(classMoveEl[i].classList.contains("posAbs")) funcMove(classMoveEl[i]);
			}
		}
	}
	
	function reselectWords(words,currentlyWord){
		/*  rnd слова */
		// if(messageToSpeak) container.appendChild(soundBtn(сomplete));

		/* когда событие произошло на панели */
		if(words) contentWords = words;
		
		
		/* if(contentWords == "I love you"){ */
		if(contentWords == wordsCompare.join(" ")){
			fliteComplete(false,"Вы выиграли!!!"); clearInterval(interval); interval = null;
		}else{
			
			if((!words) && (!interval)){
				/* значит событие по отжатию кнопки */
				// fliteComplete(false,"событие по отжатию кнопки");
				startInterval();
			}else if(words){
				
				
				// fliteComplete(false,"Добавим новый элемент");
				place.ap(freeBtn(nextWord(),function(e,t){
					reselectWords(false,t.textContent);
				}));
			}
		}
	}
	
	/* передвигатель кнопок */
	function onInterval(){
		moverBtn("MoverBtn",function(el){
			if(((parseInt(el.style.top)+el.clientHeight) > scHeight) || ((parseInt(el.style.left)+el.clientWidth) > scWidth)){
				
				el.remove();
				place.ap(freeBtn(nextWord(),function(e,t){
					reselectWords(false,t.textContent);
				}));
				// el.removeEventListener("mousedown");
				
			}else el.move(0,0.3);
		});
	}
	
	function startInterval(){
		interval = setInterval(function(){
			onInterval();
		},170);
	}
	
	if(wordsCompare.length>0){
		if(fs(wordsCompare," ")) wordsCompare = wordsCompare.split(" ");
		else wordsCompare = [wordsCompare];
		
		startInterval();			/* reselectWords(textContent всей панели, и textContent очередной кнопки) */
		let p1 = handActivePanel((headTicketMess?headTicketMess:""),reselectWords);
		p1.position(300,500);
		place.ap(p1);
		place.ap(freeBtn(nextWord(),function(e,t){
			reselectWords(false,t.textContent);
		}));
		
	}
	
}

/*	Examples:
	description = oConvert.tagsView.EntityToText(description) 
	description = oConvert.tagsView.TextToEntity(description) 
*/
const oConvert = new function(){
	this.tagsView = {
		
		EntityToText:function a(text){
			// обычно для отображения в текстовом поле, и для вставки как html
			text = str_replace('&lt;','<',text);
			text = str_replace('&gt;','>',text);
			text = str_replace('&quot;','"',text);
			return fromBaseToForm(text);
		},
		
		
		TextToEntity:function(text){
			// возможно потребуется (для записи в БД)			
			text = str_replace('<','&lt;',text);
			text = str_replace('>','&gt;',text);
			text = str_replace('"','&quot;',text);

			return text;
		}
	};
};

const shareEditTest = function(listQuery,currentlyTest,parentCurrentlyTest,yourListSavedQueryAsLink,cont,getBtnFreeLinkQuery,updateThemes,contCreate,getBtnSetCategory,updatePrivateField,updateRndField,getIconEditQuery,getBtnDelQuery,pushQueryTest,funcBack,funcInit,deliveryGrant,demoAccount){
			
	let hasCollectionquery = (currentlyTest.collectionquery == "1"?true:false);
	contCreate.clear();
	/* {.idQuery ( = .id) .idresp
		
		.query - вопрос .response - ответ
		.role .theme .trueResp = .idresp .type}
	*/
	/* когда нужно изменить одно поле теста */
	function changeHeaderDataTest(headBtnAndForm,field,funcAp){
		return getBtn(headBtnAndForm,
			function(e,bt){
				let valf = eval("currentlyTest."+field);
				if(field == 'description'){
					valf = oConvert.tagsView.EntityToText(valf);
				}
				
				fgetFormOneTextarea(e,{header:headBtnAndForm},valf,
					function(e,form,v){
						v = str_replace('\'','"',v);
						
						progress.start("start edit "+field+" field ");
						updateThemes(currentlyTest.id,field,v,
							function(resp){
								if(resp && resp.success){
									fliteComplete(false,"Успех");
									if(issetFunc(funcAp)){ funcAp(v); }
									eval("currentlyTest."+field+" = v");
									form.cont.close();
								}
							}
						);
					}
				);
			}
		);
	}
	
	function getElementCheckedPropertyForUpdate(nameProp,nameField,funcUpdate){
		let checkEl = fcr("input");
			checkEl.type = "checkbox";
			
			if((eval("currentlyTest."+nameField) == "1")){ checkEl.checked = true; }
			if(issetFunc(funcUpdate)){
				checkEl.onclick = function(e){
					progress.start("star change data for check click");
					let t = this;
					funcUpdate((t.checked?1:0),currentlyTest.id,function(resp){
						
						if(resp){
						
							if(t.checked){
								
								eval("currentlyTest."+nameField+" = 1");
								fliteComplete(false,(nameProp+" Включена"));
							
							}else{
								eval("currentlyTest."+nameField+" = 0");
								fliteComplete(false,(nameProp+" Выключена"));
							}
						}
					},checkEl);
				}
			}
		return checkEl;
	}
	
	function HandIconNoticeRecord(icon,notice){
		
		icon.addEventListener("click",function(e){
			getFormInfo(e,ftextNode(notice));
		});
		
	}
	
	contCreate.ap(changeHeaderDataTest("Название","theme",function(newVal){
		/* обновление заголовка см .setHeader() */
		if(fs(cont.hder.textContent,currentlyTest.theme)){
			cont.hder.textContent = str_replace(currentlyTest.theme,newVal,cont.hder.textContent);
		}else{
			cont.hder.textContent = 'Новое название "'+newVal+'"';
		}
	}));
	contCreate.ap(changeHeaderDataTest("Описание","description"));
	contCreate.ap(changeHeaderDataTest("Ключевые слова","keywords"));
	
	
	
	/* Изменить категорию */
	contCreate.ap(getBtnSetCategory(function(e,funcStart){
			/* func initializing start */
			shedForm(e,"Смена кагегории",function(form,blockToCont){
				
				funcStart(blockToCont,parentCurrentlyTest);
			});
			
		},function(blockToCont,currentCategory,func){
			/* func ini = funcIniContainer */
			if(currentCategory.id > 0){
				let infoBlock = fcr("text");
				iBlock = fcr("div","w100 left");
				
				// iBlock.appendChild(fcr("hr"));
				
				
				if(currentlyTest.parent != currentCategory.id){
				/* только если тест ещё не в данной категории */
				
					/* если защищенная категория - принадлежит авторизованному */
					/* то protected тут все равно будет 0 */
					if(currentCategory.protect == 0){
						
						let btn = getBtn("Поместить тест сюда",function(e,bt){
							progress.start("star change category");
							
							
							/* у теста .parent обновить на номер выбранной категории */
							updateThemes(currentlyTest.id,"parent",currentCategory.id,
								function(resp){
									if(resp && resp.success){
										fliteComplete(false,"Категория изменена");
										// blockToCont.clear();
										
										cont.setHeader('Новое расположение теста - категория "'+currentCategory.theme+'"');
									
										infoBlock.textContent = "Тест перемещен в данную категорию.";
										infoBlock.style.color = "red";
										blockToCont.appendChild(infoBlock);
										
										currentlyTest.parent = currentCategory.id;
										
										btn.remove();
									}
								}
							);
							
							
							
						});
						fhelper(btn,"Переместить тест в категорию "+currentCategory.theme);
						iBlock.appendChild(btn);
						
					}else{
						/* защищенная категория точно не принадлежит авторизованному */
						/* иначе для автооризованного protected был бы равен 0 */
						
						infoBlock.textContent = "(Категория защищена. Тест НЕ может быть перемещён)";
						infoBlock.style.color = "red";
						iBlock.appendChild(infoBlock);
						
					}
				}else{
					
					infoBlock.textContent = "Тест помещен в данную категорию.";
					infoBlock.style.color = "red";
					iBlock.appendChild(infoBlock);
				}
				
				blockToCont.ap(iBlock);
			}else{
				/* то в категории 0 - то есть не выбрана ни одна категория */
				/* значит элемент перемещать нельзя */
			}
		}
	));
	
	contCreate.ap(fcr("br"));
	contCreate.ap(getBtn("<<Назад",function(e,btnBack){
		
		if(issetFunc(funcBack)){ funcBack(); }
	}));
	
	if(funcInit){ funcInit(contCreate); }
	
	
	/* если тест не представляет собой коллекцию вопросов из других тестов */
	if(!hasCollectionquery){
		/* Добавить (вопрос) */
		contCreate.ap(getBtn("Добавить вопросы",
			function(e,bt){
				/* открыть бы стардартную форму - для добавления вопроса */
				let form = pushQueryTest(e,currentlyTest.id,function(listpushQuery){
					/* funcSuccess */
					/* в listpushQuery должно быть достаточно информации
						должны содержать поля 
						.type .query .response
					*/
					
					listQuery = listQuery.concat(listpushQuery);
					
					/* form.cont.close(); */
					shareEditTest(listQuery,currentlyTest,parentCurrentlyTest,yourListSavedQueryAsLink,cont,getBtnFreeLinkQuery,updateThemes,contCreate,getBtnSetCategory,updatePrivateField,updateRndField,getIconEditQuery,getBtnDelQuery,pushQueryTest,funcBack,funcInit,deliveryGrant,demoAccount);
				});
			}
		));
		
	}else{
		contCreate.ap(
			getBtn("Добавить",
				function(e,btnadd){
				
					yourListSavedQueryAsLink(e,btnadd,funcBack,currentlyTest);

			},"","Добавить сохранённые вопросы")
		);
	}
	
	
	contCreate.ap(ftextNode("Приватность"));
	
	let setPrivateCheck = null;
	
	if(demoAccount){
		setPrivateCheck = fcr("input");
		setPrivateCheck.type = "checkbox";
		setPrivateCheck.disabled = true;
		setPrivateCheck.checked = true;
		fhelper(setPrivateCheck,"Признак приватности Отменить нельзя - т.к. почта не подтверждена");
	}else{
		setPrivateCheck = getElementCheckedPropertyForUpdate("Приватность","private",function(enable,id,func,checkEl){
					
			updatePrivateField(enable,id,function(resp){
				if(resp){
					if(resp.success == 1){
						func(true);
						
					}else{
						fliteComplete(false,"Действие заблокировано");
						func(false);
						if(checkEl.checked){
							checkEl.checked = false;
						}else{
							checkEl.checked = true;
						}
					}
				}
			});
			
		});
	}
	
	contCreate.ap(
		setPrivateCheck
	);
	
	if(!hasCollectionquery){
	let icGrant = fcr("div","icGrant crPoint dInlBl");
		icGrant.style.marginLeft = "7px";
		fhelper(icGrant,"Передать другому пользователю");
		
		icGrant.addEventListener("click",function(e){
			
			
			let formGrant = fgetFormOneTextarea(e,{header:"Передать тест в дар"},{val:"",min:3},
				function(e,form,v){
					progress.start("start delivery grant to "+v);
					
					deliveryGrant(v,currentlyTest.id,false,function(resp){
						
						if(resp.success == 1){
							
							fliteComplete(false,"Успех");
							form.cont.close();
						}
						
					});
					
					
					
				}
			);
			formGrant.cont.btnSave.textContent = "Передать";
			
			let iconHelp = icon.txtUIcon(
				formGrant,
				function(e){
					getFormInfo(e,
					ftextNode("Здесь можно передать тест в дар другому пользователю. Для этого\
					в текстовое поле введите логин любого зарегистрированного пользователя, и соответственно\
					потом нажмите кнопку Передать.\
					Если пользователь примет тест, он станет его владельцем \
					и сможет изменять тест по своему усмотрению. В этом случае вы больше не будете его владельцем, и \
					потеряете возможность его редактировать. \
					"));
				},
				"?"
			);
			iconHelp.classList.add("fs12");
			iconHelp.classList.add("txtCenter");
			
			
		});
		
		contCreate.ap(icGrant);
	}
	
	contCreate.ap(fcr("br"));
	
	contCreate.ap(ftextNode("Рандомность"));
	contCreate.ap(
		getElementCheckedPropertyForUpdate("Рандомность","rnd",updateRndField)
	);
	
	
	/* загрузить в рабочую область таблицу
	   и заполнить её списком из всех повросов - и
	   кнопками "Изменить" и "Удалить" */
	
	
	let icNotice = null;
	// let tr = null;
	if(listQuery.length > 0){
		contCreate.ap(fcr("br"));
		contCreate.ap(ftextNode("(Коллекция из " +listQuery.length+ " вопросов)"));
	}
	
	let oHeaderArray = [
		{title:"Вопрос",key:"query",filter:true,
			format:function(resp){ return fromBaseToForm(resp);  },
			funcitem:function(td,el,i){ 
				fhelper(td.querySelector("span"),fromBaseToForm(el.response));
				
				if(!hasCollectionquery){
			
					if(el.notice && (el.notice != "")){
						
						icNotice = fcr("div","icNotice crPoint left m2 r3");
						icNotice.style.backgroundColor = "#ff3970";
						fhelper(icNotice,"О возможном несоответствии");
						
						HandIconNoticeRecord(icNotice,el.notice);
						
						td.appendChild(icNotice);
					}
					
					td.appendChild(getIconEditQuery(el,currentlyTest.id,
						function(newEl,p){ // funcSuccess update
							
							// обновить данные в listQuery
							listQuery[p] = newEl;
							progress.close("close update data table");
							// обновить таблицу
							shareEditTest(listQuery,currentlyTest,parentCurrentlyTest,yourListSavedQueryAsLink,cont,getBtnFreeLinkQuery,updateThemes,contCreate,getBtnSetCategory,updatePrivateField,updateRndField,getIconEditQuery,getBtnDelQuery,pushQueryTest,funcBack,funcInit,deliveryGrant,demoAccount);
						},i)
					);
					
				}
				
			}
		},
		{
			funcitem:function(td,el,i,tr){ 
				td.appendChild(
					getBtnDelQuery(tr,i,el.id,currentlyTest.id,
						el.response,hasCollectionquery,
						function(p){
							// delete listQuery[p];
							listQuery.splice(p,1);
							
							fliteComplete(false,"Успех");
							
							// обновить таблицу
							shareEditTest(listQuery,currentlyTest,parentCurrentlyTest,yourListSavedQueryAsLink,cont,getBtnFreeLinkQuery,updateThemes,contCreate,getBtnSetCategory,updatePrivateField,updateRndField,getIconEditQuery,getBtnDelQuery,pushQueryTest,funcBack,funcInit,deliveryGrant,demoAccount);
						}
					)
				);
					
			}
		}
		
	];
	
	if(hasCollectionquery){
		oHeaderArray.push(
			{funcitem:function(td,el,i,tr){
				
				// кнопка отвязать ссылку от теста
				td.appendChild(getBtnFreeLinkQuery(el.id,tr));
				
			}}
		);
	}
	contCreate.ap(testRenderTable(
		oHeaderArray,
		listQuery
	).table);
	
	
};

/* редактирование данных в поле, с иконкой, при нажатии которых 
функция забирает данные и создаёт выпадающий скисок под редактируемым полем */
const editDataOfActiveBlock = function(cont,el,funcGetData,args,nameRespField){
	/* ширина el и списка 80% (от cont) */
	/* данные, которые забирает функция для списка должны возвращаться в JSON */
	
	let listType = null;
	let icEdit = fcr("div","icEdit crPoint right");
	icEdit.style.width = "15px";
	icEdit.style.height = "15px";
	icEdit.addEventListener("click",function(e){
		
		if(listType){
			listType.on(); 
		
		}else{
			progress.start("start load data");
			/* вероятно отдельно получить список */
			/* затолкнуть их в массив и передать их уже в форму */
			funcGetData(args,function(resp){
				/* там поле listType [ массив значений ] */
				if(resp){
					if(eval("resp."+nameRespField).length > 0){
						listType = listCombFontFamily(el.value,function(velSelect){
							el.value = velSelect;
							listType.off();
						},eval("resp."+nameRespField));
						listType.style.width = "80%";
						listType.classList.add("fs12");
						
						cont.style.maxWidth = "195px";
						/* что бы не раздумало форму от длинных найденных цепочек слов */
						
						cont.appendChild(fcr("br"));
						cont.appendChild(listType);
						
					}else{ fliteComplete(false,"Список пустой"); }
				}
			});
		}
	});
	cont.appendChild(icEdit);
}

const getIconEditNameCategory = function(zI,category,field,minlen,funcUpdates,funcComplete){
	let icEdit = fcr("div","icEdit crPoint dInlBl");
	icEdit.style.width = "15px";
	icEdit.style.height = "15px";
	
	icEdit.addEventListener("click",function(e){
		fgetFormOneTextarea(e,{header:"Изменить название"},{val:eval("category."+field),min:minlen},
			function(e,form,v){
				progress.start("start edit name category ");
				funcUpdates(category.id,field,v,
					function(resp){
						if(resp && resp.success){
						
							fliteComplete(false,"Успех");
							form.cont.close();
							if(issetFunc(funcComplete)){ funcComplete(v); }
						}
					}
				);
			}
		).cont.style.zIndex = (parseInt(zI)+1);
	});
	return icEdit;
}

function getIconToFormHelp(arrDataHelp,w){
	
	/*	{head:,description:,more}	*/
	
	return felHelper(false,
		function(e,zI){
			let formHelp = fhelpForm(e,parseInt(zI)+1,arrDataHelp);
			if(w){ formHelp.style.width = w + "px"; }
		},
		true/*дял min icon*/
	);
}

<?php echo '</script'; ?>
>
<?php }
}
