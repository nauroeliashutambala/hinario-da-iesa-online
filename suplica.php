<?php
header('Content-Type: application/json; charset=utf-8');

$data = [
    "titulo_pt" => "Súplica geral",
    "titulo_um" => "Mbuete ngacosse",
    "texto_pt" => "Tem de dar para baixar o json",
    "texto_um" => "
 Usongi: Sandi Yehova osimbu a sangiwa; u vili kiyi osimbu a kasi ocipepi. Ukuakandu a sie onjila yaye, lu wa sia esunga a litepe lovisimilo viaye, ha tiuka ku Yehova, kuenje eye u linga ohenda, a tiuke muele ku Suku yetu, kuenje u muisa ongecelo ya lua.

###Ekongelo: Momo Yehova ukuahenda, kuenje ohenda yaye ka yi pui. Epandi liaye li enda ño hu toke kovitumbulukila viosi.

 Usongi: Ndaño akandu ene a kusuka nge, a yela ndocikokoto; ndaño a soka ndonanga yohusu, a yela ndutele. Nda tu endaila vutanya ndeci eye a kasi vutanya, osonde ya Yesu omõlaye yi tu yelisa kakandu osi.

###Ekongelo: Nda tu linga tuti, Akandu ka tu kuete, tu lilimbika, kuenda ocili vitima vietu ka cimo. Puãi nda tu litavela ovakandu etu, eye ukuacili, haiye ukuesunga, o tu ecela akandu etu; o tu upa aviho osi.

 Usongi: U wa limbika akandu aye ka tela oku sumuluha. U puãi, o litavela, ha tindukako, eye o sanga ohenda. Oco tu litaveli ovakandu etu, momo u oku lueya kuaye kua ecelua wa sumu luha.

###Ekongelo: Nda litavela kokuove ekandu liange; ongole yange sia limbikile. Nda linga, siti, Ndi litavela ku Yehova oku lueya kuange kuenje wa njupa eviho liekandu liange.

 Usongi: Ca popia Yehova citi, Nda kuliha ovisi milo ndo simili, okuti viombembua, vieyambulo havioko, kuenje eci wu vilikiyi kokuange ko kambi oku sanga nda wu sandiliyi lutima wene wosi. Oco tu konomuisi olonjila vietu, loku vi seteka, ha tu tiuka ku Yehova. Itima vietu vi imhe ovaso ku Yehova ukuailu.

###Ekongelo: Ndinge ohenda, a Suku, omo o kasi locisola. Omo liohenda yove yalua, imula oku lueya kuange. SovuiIe mima u yela, a Suku. Tumbulula vokati kange espiritu liesunga, songuile vonjila ka yi pui.

 Usongi: Una ka kulihile ekandu, Suku wo lingi sa ndekandu mekonda lietu, ha tu lingi vakue sunga lia Suku mekonda liaye. Oco ene sokololi okuti kakandu wa fi, puãi wa muili omuenyo ku Suku mekonda lia Yesu Kristu.

###Ekongelo: Kokuahe tu kuete eyovo, olio ongecelo yakandu, ndukuasi wa lua wocali caye.

 Usongi: Pandu, pandu ku Suku me konda liocali caye ca lua.
"
];

echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);