<?
	/* +---------------------------------------------------------------------------+ *\
	   | resim upload fuction.						                                   |
	\* +---------------------------------------------------------------------------+ */
				function resim_upv2($resim, $klasor, $dislem=false, $genislik=720, $yukseklik=320){
			// Resim bölümü. 
			//eðer boyutlandýrma ve resim yazýsý eklenecekde dislem true yapýlamlý geniþlik ve yukseklik tekrar belirtilmeli
			// ***eðer klasorun adý deðiþtirilirse resim_yazý fonksiyonundaki font yeniden tanýmlanmalý***
			$mesaj=array(0=>"Resim Sorunsuz yüklendi..<br>", 1=>true, 2=>"", 3=>""); // mesaj oldu/olmadý resim alaný
						$server_kaynak	= $resim["tmp_name"]; //geçici serverdaki dosya yer.
						$dizin			= $klasor; //kaydedilecek dizin yeri.
						$tip			= trim($resim["type"]);
						$boyut_kontrol	= 11000000; //resim boyut kontrol.
						$resim_boyut	= $resim["size"]; //resim boyutu
						
						// resim isimlendirme zor iþv
						$tur_dizi = array("image/jpg"=>".jpg", "image/gif"=>".gif", "image/bmp"=>".bmp", "image/png"=>".png", "image/pjpeg"=>".pjpeg", "image/jpeg"=>".jpeg");
						@$uzanti=$tur_dizi[$resim["type"]];
						 $resim_ad			= "dy_".md5(rand().$resim["name"]).$uzanti; //eklenilen resim
						$mesaj['3']=$resim["type"];
						// ayný isimde resim olma olasýlýðýna karþý
						$rsm_kont=true;
					while($rsm_kont){
							if (file_exists($dizin.$resim_ad)){ //resim tekrar kontrol.
								$resim_ad			= "dy_".md5(rand().$resim["name"]).$uzanti; // ayný olma olasýlýðý nerdeyse imkansýz
								
							}else{ $rsm_kont=false; }
					}
					$dizin_merge	= $dizin.$resim_ad; // veritabanýna yazarken dizin adý yazdýrmamak için birleþtiriliyor.
						$mesaj[2]=$resim_ad;
					
					if ($resim_boyut > $boyut_kontrol){
						$mesaj[0]= "Eklenilen resim boyutu istenilen boyuttan daha büyük..!<br>";
						$mesaj[1]=false;
						
					}
					$tur = array("image/jpg", "image/gif", "image/bmp", "image/png", "image/pjpeg", "image/jpeg");
					if (!in_array($tip, $tur)){
						$mesaj[0]= "Ýstenilen formatda resim eklenmedi veya resim alaný boþ geçildi..!<br>";
						$mesaj[1]=false;
						//exit();
					}
					
					else{
							$yukle	= move_uploaded_file($server_kaynak,$dizin_merge);
						if (!$yukle){
							$mesaj[0]= "Resim aktarýlamadý...!<br>";
							$mesaj[1]=false;
							
						}
						else{
							$mesaj[0]= "Resim baþarýyla aktarýlmýþtýr.<br>";
							$mesaj[1]=true;
						}}
						
				if($dislem==true && $mesaj[1]==true){	// dislem izin verildiyse ve uploadda hata yoksa bu iþlemi yap	
			// þimdi yeniden boyutlandýralým
			
			$bhata=resimYukle($dizin_merge,$tip,$genislik,$yukseklik,$mesaj[2]);
			//resimYukle($kaydedilecek_yer,$tipi,$genislik,$yukseklik,$yeniisim);
			
			if(false){
			// sýrada resime yazý ekleme var
		
			/*$yhata=resim_yazi($tip, $dizin_merge, "K A T E D",$genislik,$yukseklik);
			//resim_yazi($type, $kaydedilecek_yer, $yazi);
			if($yhata==1){
				$mesaj[0]= "Resim Yazýsý Eklemede Hata Oluþtu.<br>";
				$mesaj[1]=false;
					}*/
				}else{
					$mesaj[0]= "Resim Boyutlandýrmada Hata Oluþtu.<br>";
					$mesaj[1]=true;			
					
					}
		
				}
			
			return $mesaj;
			
			}
			
	/* +---------------------------------------------------------------------------+ *\
	   | resim upload fuction.						                                   |
	\* +---------------------------------------------------------------------------+ */
	
	/* +---------------------------------------------------------------------------+ *\
	   | resim boyutlandýrma fuction.						                           |
	\* +---------------------------------------------------------------------------+ */	
	function resimYukle($kaydedilecek_yer,$tipi,$genislik,$yukseklik,$yeniisim){
				$hata=0;
				$dosya=$kaydedilecek_yer; //."".$alinanin_adi;
				if(($tipi=='image/jpg') || ($tipi=='image/jpeg') || ($tipi=='image/pjpeg') ){
				$resim=imagecreatefromjpeg($dosya); 
				}elseif($tipi=='image/gif'){
				$resim=imagecreatefromgif($dosya); // Yklenen resimden oluacak yeni bir JPEG resmi oluturuyoruz..
				}elseif($tipi=='image/png'){
				$resim=imagecreatefrompng($dosya); // Yklenen resimden oluacak yeni bir JPEG resmi oluturuyoruz..
				}else{  $hata=1; }			
				
				if($hata==0){
				$boyutlar=getimagesize($dosya); // Resmimizin boyutlarn reniyoruz.				
				
				unlink($kaydedilecek_yer);
				$yeniresim=imagecreatetruecolor($genislik,$yukseklik); // Oluturulan bo resmi istediimiz boyutlara getiriyoruz..
				imagecopyresampled($yeniresim, $resim, 0, 0, 0, 0, $genislik, $yukseklik, $boyutlar[0], $boyutlar[1]);
				//$hedefdosya="resimler/".$yeniisim; // Yeni resimin kaydedilecei konumu belirtiyoruz..
				$hedefdosya=$kaydedilecek_yer;
				if(($tipi=='image/jpg') || ($tipi=='image/jpeg') || ($tipi=='image/pjpeg') ){
				imagejpeg($yeniresim,$hedefdosya,100); // Ve resmi istediimiz konuma kaydediyoruz..
				}elseif($tipi=='image/gif'){
				imagegif($yeniresim,$hedefdosya,100); // Ve resmi istediimiz konuma kaydediyoruz..
				}elseif($tipi=='image/png'){
				imagepng($yeniresim,$hedefdosya,9, PNG_NO_FILTER); // Ve resmi istediimiz konuma kaydediyoruz..
				}else{  $hata=0; }	}
			return $hata;			
				
		}

	/* +---------------------------------------------------------------------------+ *\
	   | resim boyutlandýrma fuction.						                           |
	\* +---------------------------------------------------------------------------+ */
	
	/* +---------------------------------------------------------------------------+ *\
	   | resim yazý ekleme fuction.						                           |
	\* +---------------------------------------------------------------------------+ */	
	
	function resim_yazi($type, $kaydedilecek_yer, $yazi,$genislik,$yukseklik){
			$hata=0; // $yer fontun bulunduðu alt klasorun adý
			
  switch($type) {
       case 'image/jpeg':  
           $resmimiz = imagecreatefromjpeg($kaydedilecek_yer);
           break;
		    case 'image/pjpeg':  
           $resmimiz = imagecreatefromjpeg($kaydedilecek_yer);
           break;
		    case 'image/jpg':  
           $resmimiz = imagecreatefromjpeg($kaydedilecek_yer);
           break;
       case 'image/png':
           $resmimiz = imagecreatefrompng($kaydedilecek_yer);
             break;
       case 'image/gif':
           $resmimiz = imagecreatefromgif($kaydedilecek_yer);
           break;
       default:
           echo $type;
		   $hata=1;
           break;
   }
	if($hata==0){
		
		$yy1=355;
		$xx2=10;
		
			$resim_ust = "$yy1";
			$resim_sol = "$xx2";
			$yazi_tipi = "css/Fonts/arialbd.ttf";  
			$yazi_boyutu = "25";
			$yazi_dongusu = "0";
			$kalitesi = "80";
			//$beyaz_renk = imagecolorallocate($resmimiz, 230,230,230);
			if(!file_exists($yazi_tipi))
				{ $hata=1; }else{
			$beyaz_renk = imagecolorallocatealpha($resmimiz, 255,255,255,62);
			imagettftext($resmimiz, $yazi_boyutu, $yazi_dongusu, $resim_sol, $resim_ust, $beyaz_renk, $yazi_tipi, $yazi); 
			imagejpeg($resmimiz,$kaydedilecek_yer,$kalitesi);
		
				}}
	return $hata;
	}
	/* +---------------------------------------------------------------------------+ *\
	   | resim yazý ekleme fuction.						                           |
	\* +---------------------------------------------------------------------------+ */	

 ?>