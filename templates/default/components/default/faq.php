<?if($_SESSION['reg_form_send']){echo "<div class='goodmes'>".$_SESSION['reg_form_send']."</div>";$_SESSION['reg_form_send']='';$good=1;}?>
<a name="feedbackform"></a> 
<div class="ac_box" style="margin-bottom:0px;">
		<div class="accordionButton" id="acc1">Задать вопрос</div>
		<div class="accordionContent">
			<div class="ac_content">


<div class="ac_regform">
 <form method="post" action="#form"><input type="hidden" name="proceedform[]" value="faq">

  <div class="line">
   <div class="onefirst">
    <div class="label">Ваше имя:</div>
	<div class="inp"><input type="text" name="qname" value="<?=$_POST['qname']?>" /></div>
   </div>
  </div>

  <div class="line">
   <div class="onesec mrr">
    <div class="label">Телефон:</div>
	<div class="inp"><input type="text" name="city" value="<?=$_POST['city']?>" /></div>
   </div>
   <div class="onesec">
    <div class="label">E-mail:</div>
	<div class="inp"><input type="text" name="qemail" value="<?=$_POST['qemail']?>" /></div>
   </div>
  </div>
 
  <div class="line">
   <div class="onefirst">
    <div class="label">Вопрос:</div>
	<div class="inp"><textarea name="question"><?=$_POST['question']?></textarea></div>
   </div>
  </div>

   <div class="line">
   <div class="code">
    <div class="capcha"><?=$this->core_show_capcha('code');?></div>
    <div class="label">Код с картинки:</div>
	<div class="inp"><input type="text" name="code" value="" maxlength="5"/></div>
	<div class="submit"><input type="submit" value="добавить" /></div>
   </div>
  </div>
 </form>
</div>
<?if(sizeof($this->form_error)) echo "<div class='fberror_box'><strong>Ошибка!</strong><br />- ".join("<br/>- ",$this->form_error)."</div>";?>
<div class="note">Внимание: Вы получите ответ на веш вопрос на указанный вами e-mail. Администратор сайта оставляет за собой право публиковать или нет ваш вопрос и ответ на него на сайте по своему усмотрению.</div>
			</div><!--/ac_content-->
		</div>
		<div class="bottom"></div>
	</div><!--/ac_box--><script>$(function() {hash = location.hash;if(hash=='#form') $( "#acc1" ).click();});</script>
<div class="padded">
<?
if(!sizeof($this->page_info['content'])) echo '<div class="feedbackitem"><br/>Пока нет ни одного отзыва.<br/>Вы можете оставть свой отзыв первым.</div>';
foreach($this->page_info['content'] as $item){?>
<div class="feedbackitem"> 
<div class="title"><?=$item['qdate']?><?echo $item['qname']?", ".$item['qname']:""?></div> 
<div class="txt"><?=nl2br($item['question'])?></div> 
<?if(trim($item['answer'])){?>
<div class="feed_extra_title">Ответ от ГолденЛимо</div>
<div class="feed_extra_content"><?=nl2br($item['answer'])?></div> 
<?}?>
</div> 
<?}?>
<?=$this->core_modul('navigation','basenavig')?>
</div> 