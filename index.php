<?php
session_start();
require_once("../admin/stran.php");
require_once("../admin/fu.php");
require_once('../admin/templ.php');
$perep=array();
$kol_bl=array(1000,30,20,20);
// min $kol_bl=array(1000,25,10);
// angl 12 inf 12
// берем из  общей библоитеки
//$block=array(1,"гуманітарних","професійних", "професійних (за спеціалізацією)","педблок");
mb_internal_encoding("UTF-8");
$block=$block_m;
//$parse->get_tpl('index.tpl');
$conn=obd();
//////////////
function arr2tab($data)
{$tab='<table border="1" cellpadding="1" cellspacing="0"><tr>';
for($i=0;$i<count($data);$i++)
if(count($data[0])>1){for($j=0;$j<count($data[0]);$j++)
 $tab.='<td>'.$data[$i][$j].'</td>'; $tab.='</tr><tr>';} else  $tab.='<td>'.$data[$i].'</td></tr><tr>';
 $tab.='</tr></table>';
return $tab;}
/////////////////////
function utf8_substr($str,$from,$len){
  return preg_replace('#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$from.'}'.
                       '((?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$len.'}).*#s',
                       '$1',$str);}

$sql="select Id, fam, nam, otch from pvs order by fam; ";
$result=mysql_query($sql);
while($rowp=mysql_fetch_object($result)) 
{$vik[$rowp->Id]=$rowp->fam.'&nbsp'.utf8_substr($rowp->nam,0,1).'.'.utf8_substr($rowp->otch,0,1).'.';}

/////////////
//$stud=1259;
//$stud=$_SESSION['student'];
//$sem2
//узнать курс студента и специальность
//$kurs=1;
//$sem=2;
//$spec=$_SESSION['spec'];
//$kurs=$_SESSION['kurs'];
//$spec=96;
$m=0;
$zagl='<tr><td>№</td><td>Предмет</td><td>Викладач</td></tr>';

$tab='<table width="97%"><tr><td colspan=5></td></tr> ';
$i=0;

//08-03// узнаем количество кредитов для студента
$sql="select v_gr5.id,v_kred.sem1,v_kred.sem2,v_kred.sem3,v_kred.sem4,v_kred.sem5,v_kred.sem6,v_kred.sem7,v_kred.sem8 from v_kred
inner join v_gr5 on v_gr5.spec=$spec and v_gr5.kurs=$kurs and v_gr5.end_date>now() and v_kred.v_gr= v_gr5.id order by sem";
$result=mysql_query($sql) or die("Invalid query: " . mysql_error());
//echo $sql;
while ($kredites=mysql_fetch_row($result))
{$kredit[$i++]=array($kredites[0],$kredites[1],$kredites[2],$kredites[3],$kredites[4],$kredites[5],$kredites[6],$kredites[7],$kredites[8]);
//массив кредис содержит информацию о возмжных выборах
}
//echo 'kredit'.arr2tab($kredit).'/kredit<BR>';

$i=0;
// нужно узнать что в корзине у выборця и разместить по принципу массв кредит
$sql="select v_plan.id , v_list.sem1,v_list.sem2,v_list.sem3,v_list.sem4,v_list.sem5,v_list.sem6,v_list.sem7,v_list.sem8,v_list.flag,v_prdm.kred,v_list.flag from v_list 
inner join v_plan on v_plan.id=v_list.plan and v_plan.val=1
inner join v_prdm on v_prdm.id=v_plan.prdm

where v_list.flag=1 and stud=$stud";
//inner join v_gr5 on v_gr5.id=v_plan.v_gr 
//echo $sql;
$result=mysql_query($sql) or die("Invalid query: " . mysql_error());
while ($baskets=mysql_fetch_row($result))
{
// дисциплина по плану, и выбранный семестр семестр с 1
$basket[$i++]=array($baskets[0],$baskets[1],$baskets[2],$baskets[3],$baskets[4],$baskets[5],$baskets[6],$baskets[7],$baskets[8],$baskets[9],$baskets[10],$baskets[11],$baskets[12]);
//массив кредис содержит информацию о возмжных выборах
//echo "ok";
//echo $sql;
}
//echo 'basket'.arr2tab($basket).'/basket<BR>';
//создаем массив прайс где: блок,семестр, выбрано. максимум...
//цикл возможных покупок
$pr=0;
for($i=0;$i<=count($kredit[0]);$i++)// echo $kredit[$i][0];
{
for($sem=1;$sem<=8;$sem++) //echo $kred[$i][$sem];
if($kredit[$i][$sem]!=0) //находим семесир в котором можно выбирать
{$summa=0;
$price[$pr][0]=$kredit[$i][0];
$price[$pr][1]=$sem;
//посчитать кол-во кредитов общее
//$k=1;
for($sum=0;$sum<=count($basket);$sum++)
{//echo $k++;
 //$basket[$sum][11] єто блок  if (($basket[$sum][$sem]!=0)&&($basket[$sum][11]==$kredit[$i][0])) {$summa+=$basket[$sum][10];
 if ($basket[$sum][$sem]!=0) {$summa+=$basket[$sum][10];
//echo "<BR>".$basket[$sum][11].'+'.$sem.'-'.$basket[$sum][$sem].'+'.$sem;
}
 }
$price[$pr][2]=$summa;
//
$price[$pr++][3]=$kredit[$i][$sem];
//for()
}}
//echo 'price'.arr2tab($price).'/price<BR>';
$mess_gol='<table>';
for($i_gol=0;$i_gol<count($price);$i_gol++)$mess_gol.='<tr><td width="70%"></td><td>для '.$price[$i_gol][1].' семестру</td><td bgcolor="#BBDDFF">кредитів обрано:'.$price[$i_gol][2].'&nbsp;з&nbsp;'.$price[$i_gol][3].'</td></tr>';
$mess_gol.='<table>';
//echo $mess_gol;
//var_dump($price);
// узнаем №цену"
//echo arr2tab($kredit).'<BR>'.arr2tab($basket).'<BR>'.arr2tab($price);

// for($igl=1;$igl<=count($kredit);$igl++)
// узнаем переполнение ли есть в таблице переполнение по типу: семестр, флаг переполнения

//for($semp=1;$semp<=8;$semp++)
 //{for($stroka=0;$stroka<count($basket);$stroka++)
 //{$ball=0;
 //if($basket[$stroka][$semp]==1) $ball+=$basket[$stroka][10];
 
 //}
//}


// считалось для каждого блока 
//а нужно для семестра!!!!!
//!!!!!!
$vibor='<table>';
$p=0;
for($igl=1;$igl<count($block);$igl++)


{$m=0;
//unset($perep);
//$tab0='<tr><td></td><td align="center"><big>Блок_<b>'.$block[$igl].'</b>_дисциплін</big></td><td></td><td></td><td align="right"><form id="form1" name="form1" method="post" action="..">
$tab0='<tr><td></td><td align="center"><big><b>'.$block[$igl].'</b></big></td><td></td><td></td><td align="right"><form id="form1" name="form1" method="post" action="..">

    <input type="submit" name="Submit" value="все вірно, вийти з системи" />
  </form> 
    </td><tr>';
//считаем что у человека выбрано в табличку
$vibor.='<tr>';
for($blp=0;$blp<=count($price);$blp++) //echo $price[$blp][1];
if($igl==$price[$blp][1]) 
{
echo "lala";
if($price[$blp][3]!=-1) $vibor.='<td>&nbsp;&nbsp;&nbsp;&nbsp;'.$price[$blp][1].' семестр:Всього:&nbsp;<big>'.$price[$blp][3].'</big>&nbsp;обрано: &nbsp;<big>'.$price[$blp][2].'</Big>&nbsp;</td>';
//if ($price[$blp][3]>price[$blp][2]) $perepoln=0;
//массив переполнений
//формат: sem/1-0
$perepoln=1;
if((int)$price[$blp][2]<(int)$price[$blp][3]) {$perepoln=0;
//echo 'переп'.$price[$blp][2].$price[$blp][3].$perepoln;
}

$perep[$p][0]=$price[$blp][1];
$perep[$p][1]=$perepoln;
$p++;
}
$vibor.='</tr>';
//echo 'perep'.arr2tab($perep).'/perep<BR>';
//echo arr2tab($perep);
//echo 'perep'.arr2tab($perep).'/perep<BR>';
// узнаем перечень дисциплин для семестров
//$sql1="select id, prdm from v_prdm 
$sql1="select v_prdm.id, v_prdm.prdm, v_prdm.vikl, v_prdm.kred, v_plan.id, v_plan.sem1,v_plan.sem2,v_plan.sem3,v_plan.sem4,v_plan.sem5,v_plan.sem6,v_plan.sem7,v_plan.sem8  from v_prdm 
inner join v_plan on v_plan.val=1 and v_prdm.id=v_plan.prdm
inner join v_gr5 on v_gr5.id=v_plan.v_gr and v_prdm.block=$igl and v_gr5.spec=$spec order by v_prdm.prdm ,v_gr5.sem ";
//inner join v_gr on  v_gr.id=v_plan.v_gr and v_prdm.block=$korzina[0] and v_plan.v_gr=$korzina[4] and v_prdm.sem=$korzina[1] order by v_gr.sem";

//echo ++$cet.$sql1."<BR>";
$result1=mysql_query($sql1) or die("Invalid query: " . mysql_error());
while ($prdm=mysql_fetch_row($result1))
//echo "l";
//}
{

$flag=0;
$sql_f="select flag, sem1,sem2,sem3,sem4,sem5,sem6,sem7,sem8,plan from v_list where stud=$stud and plan=$prdm[4]";
$result_f=mysql_query($sql_f) or die("Invalid query: " . mysql_error());
$flagi=mysql_fetch_row($result_f);
$flag_plan="0".$prdm[5].$prdm[6].$prdm[7].$prdm[8].$prdm[9].$prdm[10].$prdm[11].$prdm[12];
/////////// формируем семестры
$tab_sem='<table>  <tr><td>семестр:</td>';
$flag1=1;


for($i=1;$i<=8;$i++)
{$disabl='';
$flag=$flagi[0];
if($flagi[$i]<>0) {$new_val[$i]=0;$chekd='checked="1" ';$fon_sem='bgcolor="#99EE99"';} else {$new_val[$i]=1; $chekd='';$fon_sem='bgcolor="#EE9999"';}
//ищем а не переполнен ли єтот семестр и деактивируем его
//echo 'perep'.arr2tab($perep).'/perep<BR>';
/*for($dis=0;$dis<=count($perep[1]);$dis++) 
//080316???
if($perep[$dis][1]==1) 
{echo "pu";
$disabl=' disabled="disabled" ';
$new_val[$i]=0;
$st1='<i>';$st2='</i>';
$flag1=0;
} */
$flag_kr=1;
if ($kredit[0][$i]==0)$flag_kr=0;
//////////считаем и узнаем каждую строку, если стоит 1 и переполнение - дективируем
for($strk=0;$strk<count($price[0]);$strk++)
{if ((($price[$strk][1]==$i)&&(($price[$strk][2])>=$price[$strk][3]))||($flag_kr==0))
{$disabl=' disabled="disabled" ';
$new_val[$i]=0;
$st1='<i>';$st2='</i>';
$flag1=0;}
}
//echo $ddd++;
//echo 'perep'.arr2tab($perep).'/perep<BR>';
//for($dis=0;$dis<=count($perep[1]);$dis++) 
//080316???
//{echo $perep[$dis][1];
//if(($perep[$dis][1]==1)&&($perep[$dis][0]==$dis)) 
//{echo "pu";
//echo 'perep'.arr2tab($perep).'/perep<BR>';
//$disabl=' disabled="disabled" ';
//$new_val[$i]=0;
//$st1='<i>';$st2='</i>';
//$flag1=0;
//}
//}
//////
//$metka_sem="'".$prdm[4].'s'.$stud.'s'.$i.'s'.$new_val[$i]."'";
$metka_sem="'".$prdm[4].'s'.$stud.'s'.$i.'s'.$new_val[$i].'s'.$flag."'";
//$flag_plan="00011100"; //!!!
if ($flag_plan[$i]<>0) {$tab_sem.='<td '.$fon_sem.'><label onclick="sem('.$metka_sem.')">
      <input type="radio"  '.$chekd.' '.$disabl.' name="semo'.$prdm[0].'" value="'.$i.'" id="semestr_v_'.$i.'" />'.$st1.$i.$st2.'</label></td>';} else $tab_sem.='<td width="30">&nbsp;</td>';
	  $disabl='';$st1='<b>';$st2='</b>';
	  }
$tab_sem.='</tr></table>';



/////////////
if ($flagi[0]>0) $flag=1; 
//if(!in_array($prdm[1],$prdm_v))
//echo $flagi[0];
//28-02 думать как ограничить вібор
if($flag==1) $flag=0; else $flag=1;
//if($perep[$price[$blp][1]][1]==1) $flag=0;
//echo $perep[$price[$blp][1]][1];
 //
//echo $perep[$price[$blp][1]][1];
if($flagi[0]>0) 
{$fon1='bgcolor="#99EE99"';$fon='bgcolor="#88DD88"';$val=1;$mes=' &#9745;';
//зелененький
//$klkr=$klkr+$prdm[3];
} else {//красненький
 $fon1='bgcolor="#EE9999"';$fon='bgcolor="#DD9988"';
// echo "red";
//if($kor_val>=$korzina[2]) {$val=0;} else $val=1; $mes='&#9744;';
}


// считаем кол-во выбравших
//!!! 29 !!! нужно добавить семестр
$sql_c="select count(id) from v_list where plan=$prdm[4] and flag!=0 and (v_list.sem1 = '1' or v_list.sem2 = '1' or v_list.sem3 = '1' or v_list.sem4 = '1' or v_list.sem5 = '1' or v_list.sem6 = '1' or v_list.sem7 = '1' or v_list.sem8 = '1')";
//echo $sql_c;
$result_c=mysql_query($sql_c) or die("Invalid query: " . mysql_error());



$kol = mysql_fetch_array($result_c); 
$kol=$kol[0];
//$kol=mysql_num_rows($result_c);
//echo $nummer++.'_'.$kol.'+'.$kol_bl[$korzina[0]].'+<BR>';
//$flagi=mysql_fetch_row($result_f);

//$metka="'".$stud."i".$prdm[0]."i".$val."i".$flag."'";
//if($price[$igl][3])$flag=1;else $flag=0;
//if ($flag1==0) $flag=0;
//флаг должен менять значение с 0 на 1 м наоборот
$metka="'".$stud."i".$prdm[4]."i".$flag."'";
//$tabi.='<tr '.$fon1.' ><td >'.++$m.'</td><td onclick="b('.$metka.')">'.$prdm[1].'</td><td>'.$vik[$prdm[2]].'</td> <td>&nbsp;заповн.гр.:'.ceil(100*$kol/$kol_bl[$korzina[0]]).'%</td><td>'.$prdm[3].'&nbsp;крдт.</td><td>семестр:'.$tab_sem.'</td></tr>';
$tabi.='<tr '.$fon1.' ><td >'.++$m.'</td><td onclick="b('.$metka.')">'.$prdm[1].'</td><td>'.$tab_sem.'</td><td>'.$prdm[3].'&nbsp;крдт.</td> <td>&nbsp;заповн.гр.:'.ceil(100*$kol/$kol_bl[$igl]).'%</td><td></td></tr>';
}

//echo $vibor;
//$tabi=$tab00.$tab0.$vibor.'</tr>'.$tabi;
//$tabi=$tab00.$tab0.'<td colspan=5 align="right">'.$vibor.'</td></tr>'.$tabi;
$tabi=$tab00.$tab0.'<td colspan=5 align="right"></td></tr>'.$tabi;
$tab.='<tr><td colspan=5><HR></td></tr>'.$tabi;
$tabi='';
//$tab.='<tr><td>№</td><td>Предмет</td><td>Викладач</td></tr>';
//$tab.='<tr><td>+++</td><tr></tr>';
//}
//*/
}
//echo $vibor;

//!! думай!!!!
//$vibor.='</table>';
$tab=$mess_gol.$tab;
//echo $vibor;
//$tab_gol=$tab;
//echo $tab;
//}
echo date('H:i:s', time());
//header('Refresh: 20; url=' .$_SERVER['PHP_SELF']); 
?>
