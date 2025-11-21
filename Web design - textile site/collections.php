<?php
 goto spZdl; yo2o0: $products_data = json_decode($products_json, true); goto D_G48; ISW0x: $products_json = file_get_contents(__DIR__ . "\57\x70\x72\x6f\144\x75\143\x74\163\x2e\152\x73\157\x6e"); goto yo2o0; spZdl: require_once "\x69\x6e\x63\x6c\x75\x64\145\x73\x2f\x68\145\141\x64\x65\x72\56\x70\150\x70"; goto ISW0x; ssGoJ: ?>
</div></div><?php  goto T1pwj; tDAo3: foreach ($products_data as $category => $products) { ?>
<div class="mb-5 col-12"><h2 class="text-center section-title"><?php  echo ucfirst($category); ?>
</h2><div class="row"><?php  foreach ($products as $product) { ?>
<div class="col-md-4 mb-4"><div class="collection-item"><div class="image-hover-effect"><img alt="<?php  echo htmlspecialchars($product["\x6e\141\x6d\x65"]); ?>
"class="img-fluid"src="<?php  echo htmlspecialchars($product["\x69\x6d\x61\147\145"]); ?>
"></div><div class="collection-info p-3"><h5 class="card-title"><?php  echo htmlspecialchars($product["\x6e\x61\x6d\x65"]); ?>
</h5><p class="card-text"><?php  echo htmlspecialchars($product["\x64\145\x73\143\162\x69\160\x74\151\x6f\x6e"]); ?>
</p><a class="btn btn-primary w-100"href="product-detail.php?id=<?php  echo $product["\x69\144"]; ?>
">Detaylar</a></div></div></div><?php  } ?>
</div></div><?php  } goto ssGoJ; D_G48: ?>
<div class="mb-5 bg-texture page-header py-5"style="background-image:linear-gradient(rgba(0,0,0,.7),rgba(0,0,0,.7)),url(img/collections/collections_header.webp)"><div class="container"><h1 class="text-center display-4 mb-0 text-white">Koleksiyonlarımız</h1></div></div><div class="container py-5"><div class="row"><?php  goto tDAo3; T1pwj: require_once "\151\156\143\x6c\165\x64\145\163\x2f\x66\x6f\157\x74\x65\x72\x2e\x70\150\160"; goto mMBCn; mMBCn: ?>