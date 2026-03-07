<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
<title>Menü</title>
<style>
*{box-sizing:border-box;margin:0;padding:0;-webkit-tap-highlight-color:transparent}
:root{
  --primary:#c8922a;
  --primary-light:#fdf3e3;
  --bg:#d4b08c;
  --surface:#ffffff;
  --text:#1a1a1a;
  --text-sec:#555;
  --muted:#999;
  --border:#e8ddd0;
  --r:8px;
}
html,body{height:100%;overflow:hidden;background:var(--bg);color:var(--text);font-family:'Segoe UI',system-ui,sans-serif;font-size:14px;}
body{display:flex;flex-direction:column;}

/* HEADER */
.hdr{flex-shrink:0;background:var(--bg);border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:center;padding:0 14px;height:58px;}
.brand{display:flex;align-items:center;}
.brand-name{font-size:1.05rem;font-weight:900;color:var(--text);letter-spacing:3px;text-transform:uppercase;}

/* PAGE */
.page{display:none;flex:1;overflow:hidden;flex-direction:column;}
.page.active{display:flex;}
.scroll-area{flex:1;overflow-y:auto;-webkit-overflow-scrolling:touch;padding-bottom:30px;}

/* KATEGORİ GRID */
.cat-grid{display:grid;grid-template-columns:repeat(auto-fill,155px);gap:12px;background:none;padding:14px;justify-content:center;}
.cat-card{background:var(--surface);cursor:pointer;overflow:hidden;position:relative;transition:transform .15s;border-radius:18px;width:155px;}
.cat-card:active{transform:scale(.97);}
/* Görsel alanı – sabit kare */
.cat-img-wrap{width:155px;height:130px;overflow:hidden;position:relative;background:#c9a882;}
.cat-img-wrap img{width:100%;height:100%;object-fit:cover;display:block;transition:transform .35s;}
.cat-card:hover .cat-img-wrap img{transform:scale(1.06);}
/* Görsel yoksa tek düze renk */
.cat-img-empty{position:absolute;inset:0;background:#c9a882;}
/* İsim altta – ince şerit */
.cat-label{padding:9px 8px 10px;text-align:center;background:var(--surface);}
.cat-label .cn{font-size:.66rem;font-weight:800;letter-spacing:1.4px;text-transform:uppercase;color:var(--text);line-height:1.3;}

/* ARAMA */
.search-bar{flex-shrink:0;padding:10px 14px 6px;background:var(--bg);}
.search-input-wrap{position:relative;}
.search-input{width:100%;padding:10px 36px 10px 14px;border-radius:24px;border:none;background:rgba(255,255,255,.75);font-size:.85rem;color:var(--text);outline:none;font-family:inherit;}
.search-input::placeholder{color:#a08060;}
.search-input:focus{background:#fff;}
.search-clear{position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;font-size:1rem;color:#a08060;cursor:pointer;display:none;line-height:1;padding:2px 4px;}
/* ARAMA SONUÇLARI */
.search-results{flex:1;overflow-y:auto;-webkit-overflow-scrolling:touch;padding-bottom:30px;display:none;}
.search-results.visible{display:block;}
.search-empty{text-align:center;padding:40px 20px;color:var(--muted);font-size:.85rem;}
.sr-cat-label{font-size:.6rem;font-weight:800;letter-spacing:1.2px;text-transform:uppercase;color:var(--muted);padding:12px 14px 4px;}
/* ÜRÜN SAYFASI */
.det-hdr{flex-shrink:0;background:var(--bg);border-bottom:1px solid var(--border);display:flex;align-items:center;gap:10px;padding:0 14px;height:50px;}
.back-btn{background:none;border:none;color:var(--text);font-size:1.3rem;cursor:pointer;padding:4px 6px 4px 0;display:flex;align-items:center;line-height:1;}
.det-cat-name{font-size:.82rem;font-weight:800;flex:1;text-transform:uppercase;letter-spacing:.8px;}
.det-count{font-size:.62rem;color:var(--muted);font-weight:600;background:var(--surface);padding:3px 9px;border-radius:20px;border:1px solid var(--border);}
.product-card{display:flex;align-items:center;gap:12px;padding:12px 14px;background:var(--surface);border-bottom:1px solid var(--border);cursor:pointer;transition:background .12s;}
.product-card:active{background:#fdf3e3;}
.pinfo{flex:1;min-width:0;}
.pname{font-size:.88rem;font-weight:700;color:var(--text);line-height:1.35;margin-bottom:4px;}
.pdesc{font-size:.7rem;color:var(--muted);line-height:1.5;margin-bottom:6px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
.pprice{font-size:.95rem;font-weight:800;color:var(--primary);}
.pimg-wrap{flex-shrink:0;width:80px;height:80px;border-radius:var(--r);overflow:hidden;background:var(--bg);display:flex;align-items:center;justify-content:center;}
.pimg-wrap img{width:100%;height:100%;object-fit:cover;}
.pimg-empty{font-size:2rem;opacity:.12;}

/* MODAL */
.modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:300;align-items:flex-end;justify-content:center;}
.modal-overlay.open{display:flex;}
.modal-sheet{background:#fff;width:100%;max-width:540px;border-radius:20px 20px 0 0;overflow:hidden;max-height:88vh;display:flex;flex-direction:column;animation:slideUp .25s cubic-bezier(.32,1.5,.6,1);}
@keyframes slideUp{from{transform:translateY(100%)}to{transform:translateY(0)}}
.modal-img-area{flex-shrink:0;position:relative;}
.modal-img{width:100%;height:230px;object-fit:cover;display:block;}
.modal-img-empty{height:80px;display:flex;align-items:center;justify-content:center;font-size:4rem;opacity:.1;background:var(--bg);}
.modal-close{position:absolute;top:10px;right:10px;background:rgba(0,0,0,.4);border:none;color:#fff;width:30px;height:30px;border-radius:50%;font-size:.9rem;cursor:pointer;z-index:10;display:flex;align-items:center;justify-content:center;}
.modal-body{padding:18px 18px 36px;overflow-y:auto;}
.modal-title{font-size:1rem;font-weight:800;margin-bottom:7px;line-height:1.35;text-transform:uppercase;letter-spacing:.5px;}
.modal-desc{font-size:.78rem;color:var(--text-sec);line-height:1.65;margin-bottom:14px;}
.modal-price{font-size:1.3rem;font-weight:800;color:var(--primary);}
</style>
</head>
<body>

<div class="hdr">
  <div class="brand">
    <div class="brand-name" id="brandName">Menü</div>
  </div>
</div>

<!-- SAYFA 1: KATEGORİ KARTLARI -->
<div class="page active" id="pageCats">
  <div class="search-bar">
    <div class="search-input-wrap">
      <input class="search-input" id="searchInput" type="text" placeholder="Ürün ara..." oninput="onSearch(this.value)" autocomplete="off">
      <button class="search-clear" id="searchClear" onclick="clearSearch()">&#10005;</button>
    </div>
  </div>
  <div class="search-results" id="searchResults"></div>
  <div class="scroll-area" id="catScrollArea">
    <div class="cat-grid">
<?php $ci = 0; ?>
<?php $__currentLoopData = $products->sortKeys(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<?php
  $imgUrl = $catImages[$cat] ?? null;
  $cls    = 'c'.($ci % 10);
  $eid    = 'sec_'.preg_replace('/[^a-zA-Z0-9]/','_',$cat);
  $ci++;
?>
      <div class="cat-card <?php echo e($cls); ?>" onclick="openCat('<?php echo e($eid); ?>')">
        <div class="cat-img-wrap">
          <?php if($imgUrl): ?>
          <img src="<?php echo e($imgUrl); ?>" alt="<?php echo e($cat); ?>" loading="lazy">
          <?php else: ?>
          <div class="cat-img-empty"></div>
          <?php endif; ?>
        </div>
        <div class="cat-label">
          <div class="cn"><?php echo e($cat ?: 'Genel'); ?></div>
        </div>
      </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
  </div>
  </div>
</div>

<!-- SAYFA 2: ÜRÜN LİSTESİ -->
<div class="page" id="pageProducts">
  <div class="det-hdr">
    <button class="back-btn" onclick="goBack()">&#8592;</button>
    <div class="det-cat-name" id="detCatName"></div>
    <div class="det-count" id="detCount"></div>
  </div>
  <div class="scroll-area" id="productScrollArea">
<?php $__currentLoopData = $products->sortKeys(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<?php $eid = 'sec_'.preg_replace('/[^a-zA-Z0-9]/','_',$cat); ?>
    <div id="<?php echo e($eid); ?>" style="display:none">
<?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <div class="product-card" onclick="openModal(<?php echo e($p->id); ?>)">
        <div class="pinfo">
          <div class="pname"><?php echo e($p->name); ?></div>
          <?php if($p->description): ?><div class="pdesc"><?php echo e($p->description); ?></div><?php endif; ?>
          <div class="pprice"><?php echo e(number_format($p->price,2,',','.')); ?> &#8378;</div>
        </div>
        <div class="pimg-wrap">
          <?php if($p->image_url): ?>
          <img src="<?php echo e($p->image_url); ?>" alt="<?php echo e($p->name); ?>" loading="lazy" onerror="this.parentNode.innerHTML='<span class=pimg-empty>&#9749;</span>'">
          <?php else: ?>
          <span class="pimg-empty">&#9749;</span>
          <?php endif; ?>
        </div>
      </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </div>
</div>

<!-- MODAL -->
<div class="modal-overlay" id="modalOverlay" onclick="closeModal(event)">
  <div class="modal-sheet">
    <div class="modal-img-area" id="modalImgArea">
      <button class="modal-close" onclick="closeModalDirect()">&#10005;</button>
    </div>
    <div class="modal-body">
      <div class="modal-title" id="modalTitle"></div>
      <div class="modal-desc"  id="modalDesc"></div>
      <div class="modal-price" id="modalPrice"></div>
    </div>
  </div>
</div>

<?php
$productsJson = $products->flatten()->map(fn($p) => [
  'id'    => $p->id,
  'name'  => $p->name,
  'desc'  => $p->description ?? '',
  'price' => number_format($p->price, 2, ',', '.'),
  'img'   => $p->image_url ?? '',
])->keyBy('id')->toJson(JSON_HEX_TAG|JSON_HEX_QUOT);
$catMap = [];
foreach($products->sortKeys() as $cat => $items){
  $catMap['sec_'.preg_replace('/[^a-zA-Z0-9]/','_',$cat)] = ['name'=>($cat?:'Genel'),'count'=>$items->count()];
}
$catMapJson = json_encode($catMap, JSON_HEX_TAG|JSON_HEX_QUOT|JSON_UNESCAPED_UNICODE);
?>

<script>
<?php
$allProductsJson = collect();
foreach($products->sortKeys() as $cat => $items){
  $eid2 = 'sec_'.preg_replace('/[^a-zA-Z0-9]/','_',$cat);
  $allProductsJson[$eid2] = [
    'name'  => $cat ?: 'Genel',
    'items' => $items->map(fn($p)=>['id'=>$p->id,'name'=>$p->name,'desc'=>$p->description??'','price'=>number_format($p->price,2,',','.'),'img'=>$p->image_url??''])->values()
  ];
}
?>
var ALL_PRODUCTS = <?php echo json_encode($allProductsJson, JSON_HEX_TAG|JSON_HEX_QUOT|JSON_UNESCAPED_UNICODE); ?>;
var PRODUCTS = <?php echo $productsJson; ?>;
var CAT_MAP  = <?php echo $catMapJson; ?>;
var currentSec = null;
(function(){
  var saved=JSON.parse(localStorage.getItem('menu_theme')||'null');
  if(saved){Object.keys(saved).forEach(function(k){document.documentElement.style.setProperty(k,saved[k]);});}
  try{var s=JSON.parse(localStorage.getItem('pos_settings')||'{}');if(s.isletmeAdi){document.getElementById('brandName').textContent=s.isletmeAdi;}}catch(e){}
})();
window.addEventListener('storage',function(e){
  if(e.key==='menu_theme'){try{var t=JSON.parse(e.newValue||'{}');Object.keys(t).forEach(function(k){document.documentElement.style.setProperty(k,t[k]);});}catch(ex){}}
  if(e.key==='pos_settings'){try{var s=JSON.parse(e.newValue||'{}');if(s.isletmeAdi){document.getElementById('brandName').textContent=s.isletmeAdi;}}catch(ex){}}
});
function onSearch(q){
  var clearBtn=document.getElementById('searchClear');
  var results=document.getElementById('searchResults');
  var catArea=document.getElementById('catScrollArea');
  q=q.trim();
  clearBtn.style.display=q?'block':'none';
  if(!q){results.classList.remove('visible');catArea.style.display='';return;}
  catArea.style.display='none';
  results.classList.add('visible');
  var html='';
  var found=0;
  Object.values(ALL_PRODUCTS).forEach(function(cat){
    var hits=cat.items.filter(function(p){return p.name.toLowerCase().indexOf(q.toLowerCase())>-1||(p.desc&&p.desc.toLowerCase().indexOf(q.toLowerCase())>-1);});
    if(!hits.length)return;
    html+='<div class="sr-cat-label">'+cat.name+'</div>';
    hits.forEach(function(p){
      found++;
      html+='<div class="product-card" onclick="openModal('+p.id+')"><div class="pinfo"><div class="pname">'+p.name+'</div>'+(p.desc?'<div class="pdesc">'+p.desc+'</div>':'')+'<div class="pprice">'+p.price+' ₺</div></div><div class="pimg-wrap">'+(p.img?'<img src="'+p.img+'" alt="'+p.name+'" loading="lazy">':'<span class="pimg-empty">&#9749;</span>')+'</div></div>';
    });
  });
  results.innerHTML=found?html:'<div class="search-empty">"'+q+'" için sonuç bulunamadı</div>';
}
function clearSearch(){
  document.getElementById('searchInput').value='';
  onSearch('');
}
function openCat(secId){
  if(currentSec){var prev=document.getElementById(currentSec);if(prev)prev.style.display='none';}
  currentSec=secId;
  var sec=document.getElementById(secId);if(sec)sec.style.display='block';
  var info=CAT_MAP[secId]||{name:'Genel',count:0};
  document.getElementById('detCatName').textContent=info.name;
  document.getElementById('detCount').textContent=info.count+' ürün';
  document.getElementById('pageCats').classList.remove('active');
  document.getElementById('pageProducts').classList.add('active');
  document.getElementById('productScrollArea').scrollTop=0;
}
function goBack(){
  document.getElementById('pageProducts').classList.remove('active');
  document.getElementById('pageCats').classList.add('active');
}
function openModal(id){
  var p=PRODUCTS[id];if(!p)return;
  var area=document.getElementById('modalImgArea');
  var closeBtn='<button class="modal-close" onclick="closeModalDirect()">&#10005;</button>';
  if(p.img){area.innerHTML='<img class="modal-img" src="'+p.img+'" alt="">'+closeBtn;}
  else{area.innerHTML='<div class="modal-img-empty">&#9749;</div>'+closeBtn;}
  document.getElementById('modalTitle').textContent=p.name;
  document.getElementById('modalDesc').textContent=p.desc||'';
  document.getElementById('modalPrice').textContent=p.price+' ₺';
  document.getElementById('modalOverlay').classList.add('open');
}
function closeModal(e){if(e.target===document.getElementById('modalOverlay'))closeModalDirect();}
function closeModalDirect(){document.getElementById('modalOverlay').classList.remove('open');}
</script>
</body>
</html>
<?php /**PATH C:\Users\brusk\OneDrive\Masaüstü\eto\laravel-cafe\resources\views/menu/index.blade.php ENDPATH**/ ?>