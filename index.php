<?php
session_start();
require_once("../admin/stran.php");
require_once("../admin/fu.php");
require_once('../admin/templ.php');

$kol_bl=array(1000,30,20,20);
// min $kol_bl=array(1000,25,10);
// angl 12 inf 12
$block=array(1,"гуманітарних","професійних", "професійних (за спеціалізацією)");
mb_internal_encoding("UTF-8");
//$parse->get_tpl('index.tpl');
$conn=obd();
//////////////
function utf8_substr($str,$from,$len){
# utf8 substr
# www.yeap.lv
  return preg_replace('#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$from.'}'.
                       '((?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$len.'}).*#s',
                       '$1',$str);}

$sql="select Id, fam, nam, otch from pvs order by fam; ";
$result=mysql_query($sql);
while($rowp=mysql_fetch_object($result)) 
{$vik[$rowp->Id]=$rowp->fam.'&nbsp'.utf8_substr($rowp->nam,0,1).'.'.utf8_substr($rowp->otch,0,1).'.';}

/////////////
//$stud=2940;

//$stud=$_SESSION['student'];
//$sem2
//узнать курс студента и специальность
//$kurs=6;
//$sem=2;
//$spec=$_SESSION['spec'];
//$kurs=$_SESSION['kurs'];
//$spec=97;
$m=0;
$zagl='<tr><td>№</td><td>Предмет</td><td>Викладач</td></tr>';


$tab='<table><tr><td colspan=5></td></tr> ';
//27-02// узнаем количество кредитов для студента
$sql="select v_gr.block,v_kred.sem1,v_kred.sem2,v_kred.sem3,v_kred.sem4,v_kred.sem5,v_kred.sem6,v_kred.sem7,v_kred.sem8 from v_kred
inner join v_gr on v_gr.spec=$spec and v_gr.kurs=$kurs and v_gr.end_date>now() and v_kred.v_gr= v_gr.id order by block, sem";
$result=mysql_query($sql) or die("Invalid query: " . mysql_error());
while ($kredites=mysql_fetch_row($result))
{echo "ok";
echo $sql;}

//block, sem, kred, end_date, id from v_gr where spec=$spec and kurs=$kurs and end_date>now() order by block, sem";

// узнаем количество кредитов для студента
$sql="select block, sem, kred, end_date, id from v_gr where spec=$spec and kurs=$kurs and end_date>now() order by block, sem";
//echo $sql;
$result=mysql_query($sql) or die("Invalid query: " . mysql_error()); $i=0;
while ($korzina=mysql_fetch_row($result))
{//echo '<HR>';
$klkr=0;
$a=0;
$tab0='<tr><td colspan=2 align="center"><big>Блок_<b>'.$block[$korzina[0]].'</b>_дисциплін</big></td>';
//$tab.=$zagl;
$tab.='<tr>';
$basket[$i++]=array($korzina[0],$korzina[1],$korzina[2]);
//
$sql1="select v_prdm.kred, v_prdm.prdm from v_prdm
inner join v_list on v_list.stud=$stud and flag!=0";
//260216
//inner join v_list on v_prdm.id=v_list.prdm and v_list.stud=$stud and flag!=0";
//echo $sql1;
//$prdm_v=0;
//260211
/*$result1=mysql_query($sql1) or die("Invalid query: " . mysql_error());
while ($kor=mysql_fetch_row($result1))
{
$prdm_v[$a++]=$kor[1];
}*/
//print_r ($prdm_v);
//echo '<hr>';
//узнаем заполненность корзины в kor_val
//$sql1="select v_prdm.kred, v_prdm.prdm from v_prdm
//inner join v_list on v_prdm.id=v_list.prdm and v_list.stud=$stud and flag!=0 and v_prdm.block=$korzina[0] and v_prdm.sem=$korzina[1]";

$sql1="select v_prdm.kred, v_prdm.prdm, v_plan.id from v_prdm
inner join v_list on v_list.stud=$stud and flag!=0 and v_prdm.block=$korzina[0]
inner join v_plan on v_plan.id=v_list.plan and v_plan.prdm=v_prdm.id 
inner join v_gr on v_gr.sem=$korzina[1] and v_gr.id=v_plan.v_gr";
//echo $sql1;
$kor_val=0;
$result1=mysql_query($sql1) or die("Invalid query: " . mysql_error());
while ($kor=mysql_fetch_row($result1))
{
$kor_val=$kor_val+$kor[0];
//echo $i++;
$prdm_v[$a++]=$kor[1];
}

// узнаем перечень дисциплин для семестров
//$sql1="select id, prdm from v_prdm 
$sql1="select v_prdm.id, v_prdm.prdm, v_prdm.vikl, v_prdm.kred, v_plan.id, v_plan.sem1,v_plan.sem2,v_plan.sem3,v_plan.sem4,v_plan.sem5,v_plan.sem6,v_plan.sem7,v_plan.sem8  from v_prdm 
inner join v_plan on v_plan.val=1 and v_prdm.id=v_plan.prdm
inner join v_gr on v_gr.id=v_plan.v_gr and v_prdm.block=$korzina[0] and v_gr.sem=$korzina[1] and v_gr.spec=$spec order by v_prdm.prdm ,v_gr.sem ";
//inner join v_gr on  v_gr.id=v_plan.v_gr and v_prdm.block=$korzina[0] and v_plan.v_gr=$korzina[4] and v_prdm.sem=$korzina[1] order by v_gr.sem";
//echo $sql1.'<BR>';
$result1=mysql_query($sql1) or die("Invalid query: " . mysql_error());
while ($prdm=mysql_fetch_row($result1))
{
$flag_plan="0".$prdm[5].$prdm[6].$prdm[7].$prdm[8].$prdm[9].$prdm[10].$prdm[11].$prdm[12];
//echo ++$mmm.$prdm[1].'<BR>';
$sql_f="select flag, sem1,sem2,sem3,sem4,sem5,sem6,sem7,sem8,plan from v_list where stud=$stud and plan=$prdm[4]";
//$sql_f="select flag, sem1,sem2,sem3,sem4,sem5,sem6,sem7,sem8,plan from v_list 
//inner join where stud=$stud and plan=$prdm[4]";
//echo $sql_f;
$result_f=mysql_query($sql_f) or die("Invalid query: " . mysql_error());
$flagi=mysql_fetch_row($result_f);
/////////// формируем семестры
$tab_sem='<table>  <tr><td>семестр:</td>';
for($i=1;$i<=8;$i++)
{
if($flagi[$i]<>0) {$new_val[$i]=0;$chekd='checked="1" ';$fon_sem='bgcolor="#99EE99"';} else {$new_val[$i]=1; $chekd='';$fon_sem='bgcolor="#EE9999"';}
$metka_sem="'".$prdm[4].'s'.$stud.'s'.$i.'s'.$new_val[$i]."'";
//$flag_plan="00011100"; //!!!
if ($flag_plan[$i]<>0) {$tab_sem.='<td '.$fon_sem.'><label onclick="sem('.$metka_sem.')">
      <input type="radio" '.$chekd.' name="semo'.$prdm[0].'" value="'.$i.'" id="semestr_v_'.$i.'" />'.$i.'</label></td>';} else $tab_sem.='<td></td>';
	  }
$tab_sem.='</tr></table>';
/////////////
if ($flagi[0]>0) $flag=1;
//if(!in_array($prdm[1],$prdm_v))
echo $flagi[0];
if($flagi[0]>0) 
{$fon1='bgcolor="#99EE99"';$fon='bgcolor="#88DD88"';$val=1;$mes=' &#9745;';
//зелененький
//$klkr=$klkr+$prdm[3];
} else {//красненький
 $fon1='bgcolor="#EE9999"';$fon='bgcolor="#DD9988"';
// echo "red";
if($kor_val>=$korzina[2]) {$val=0;} else $val=1; $mes='&#9744;';}
//// ищем єту дисциплину среди вібранніхша)
//2602
if(in_array($prdm[1],$prdm_v)) {$val=0;}
//print_r ($prdm_v);
//echo "<BL>";


// считаем кол-во выбравших
$sql_c="select count(id) from v_list where plan=$prdm[4] and flag!=0";
//echo $sql_c;
$result_c=mysql_query($sql_c) or die("Invalid query: " . mysql_error());
$kol = mysql_fetch_array($result_c); 
$kol=$kol[0];
//$kol=mysql_num_rows($result_c);
//echo $nummer++.'_'.$kol.'+'.$kol_bl[$korzina[0]].'+<BR>';
//$flagi=mysql_fetch_row($result_f);

//$metka="'".$stud."i".$prdm[0]."i".$val."i".$flag."'";
$metka="'".$stud."i".$prdm[4]."i".$val."i".$flag."'";
//$tabi.='<tr '.$fon1.' ><td >'.++$m.'</td><td onclick="b('.$metka.')">'.$prdm[1].'</td><td>'.$vik[$prdm[2]].'</td> <td>&nbsp;заповн.гр.:'.ceil(100*$kol/$kol_bl[$korzina[0]]).'%</td><td>'.$prdm[3].'&nbsp;крдт.</td><td>семестр:'.$tab_sem.'</td></tr>';
$tabi.='<tr '.$fon1.' ><td >'.++$m.'</td><td onclick="b('.$metka.')">'.$prdm[1].'</td><td>'.$tab_sem.'</td><td>'.$prdm[3].'&nbsp;крдт.</td> <td>&nbsp;заповн.гр.:'.ceil(100*$kol/$kol_bl[$korzina[0]]).'%</td><td></td></tr>';
}
$m=0;
//echo '<HR>'.$korzina[0].'<HR>';
$tabi=$tab00.$tab0.'<td colspan=3>Всього кредитів:<b>'.$korzina[2].'</b>, з них використано: <b>'.$kor_val.'</b></td></tr>'.$tabi;
$tab.='<tr><td colspan=5><HR></td></tr>'.$tabi;
$tabi='';
//$tab.='<tr><td>№</td><td>Предмет</td><td>Викладач</td></tr>';
//$tab.='<tr><td>+++</td><tr></tr>';
}

$tab_gol=$tab;

?>