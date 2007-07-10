jQuery(document).ready(function(){

//  jQuery("tr.list:nth-child(odd)").addClass("odd");  
  jQuery("tr.project_info").bind("mouseover", function(){
          jQuery(this).find("td").css('backgroundColor','#343434');
         });
  jQuery("tr.project_info").bind("mouseout", function(){
          jQuery(this).find("td").css('backgroundColor','#292929');
        });
  jQuery("tr.build").bind("mouseover", function(){
          jQuery(this).find("td").css('backgroundColor','#292929');
         });
  jQuery("tr.build").bind("mouseout", function(){
          jQuery(this).find("td").css('backgroundColor','#000000');
        });      
  jQuery('tr.build').css('display','none');

});

function category_toggle (elem_selector){
  var elem = document.getElementById(elem_selector);
  var toggle = jQuery(elem).toggle();    
  var name_toggle = document.getElementById(elem_selector + '_toggle');
  if (toggle.css('display') != 'block')
      jQuery(name_toggle).attr('src','/images/icon/open.gif');
  else 
      jQuery(name_toggle).attr('src','/images/icon/close.gif');
}

function builds_toggle (elem_selector){
  var toggle = null;
  jQuery('tr').each(function(){
    if (jQuery(this).attr('class') == 'build '+ elem_selector)
      toggle = jQuery(this).toggle();  
  });
  var name_toggle = document.getElementById(elem_selector + '_toggle');
  if (toggle.css('display') != 'table-row')
      jQuery(name_toggle).find('img').attr('src','/images/icon/plus.gif');
  else 
      jQuery(name_toggle).find('img').attr('src','/images/icon/minus.gif');
}

function info_toggle (elem_selector){
  var elem = document.getElementById(elem_selector);  
  var toggle = jQuery(elem).toggle();    
  var name_toggle = document.getElementById(elem_selector + '_toggle');
  if (toggle.css('display') != 'block')
      jQuery(name_toggle).find('img').attr('src','/images/icon/plus.gif');
  else 
      jQuery(name_toggle).find('img').attr('src','/images/icon/minus.gif');
      
}

