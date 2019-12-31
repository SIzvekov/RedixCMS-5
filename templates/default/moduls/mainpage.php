<?if($this->thishomepage){
/*
$array = array(
array("img"=>"img8.jpg","title"=>"автопарк лимузинов напрокат","url"=>"/avtopark"),
array("img"=>"img1.jpg","title"=>"лимузин на свадьбу","url"=>"/specpredlojeniya"),
array("img"=>"img5.jpg","title"=>"встреча из роддома на лимузине","url"=>"/uslugi_i_ceni/svadba"),
array("img"=>"img2.jpg","title"=>"прокат лимузина на день рожденье","url"=>"/uslugi_i_ceni/den_rojdeniya"),
array("img"=>"img6.jpg","title"=>"романтическое свидание в лимузине","url"=>"/uslugi_i_ceni/romanticheskoe_svidanie"),
array("img"=>"img4.jpg","title"=>"аренда лимузина для трансфера","url"=>"/uslugi_i_ceni/vstrecha_iz_roddoma"),
array("img"=>"img7.jpg","title"=>"украшение для лимузина к любому событию","url"=>"/uslugi_i_ceni/transfer"),
array("img"=>"img9.jpg","title"=>"обслуживание и ремонт лимузинов","url"=>"/uslugi_i_ceni/ukrasheniya"),
);
*/
$startedline = 0;
$i=0;
$line = 0;
$itemsPerLine = 3;
$numOfLines = ceil(sizeof($array)/$itemsPerLine);
foreach($array as $item)
{
	$i++;

	if(!$startedline)
	{
		$line++;
		echo $line<$numOfLines?'<div class="row"><ul class="list">':'<div class="row2"><div class="row-holder"><ul class="list">';
		$startedline = 1;
	}
	?>
<li>
	<div class="item-holder">
		<a href="<?=$item['url']?>"><img src="/images/mainpage/<?=$item['img']?>" width="200" height="150" alt="<?=$item['title']?>" /></a>
		<div class="description" style="padding:0px 5px;width:200px;overflow:hidden;">
			<div class="holder">
				<div class="frame" style="padding:5px 0;">
					<strong class="title" style="line-height:18px;"><a style="text-decoration: none;" href="<?=$item['url']?>"><?=$item['title']?></a></strong>
				</div>
			</div>
		</div>
	</div>
</li>
<?
	if($i==$itemsPerLine)
	{
		echo $line<$numOfLines?'</ul></div>':'</ul></div></div>';
		$i = 0;
		$startedline = 0;
	}
}
if($startedline)
{
	echo $line<$numOfLines?'</ul></div>':'</ul></div></div>';
}
?>

<div id="seo-zone" style=""></div>

<?}?>
