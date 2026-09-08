<?php
/**
 * Accueil cinématique — L.A Environnement.
 *
 * Même mécanique que l'accueil d'Alliance Groupe (demande de Fabrice) :
 * défilement narratif piloté par GSAP + ScrollTrigger + Lenis. Hero, bandeau
 * accéléré par la vitesse de défilement, tableau épinglé en Ken Burns,
 * chapitres en parallaxe, scène unique (dissolution en poussière de feuilles →
 * prestations qui sortent de la paume → grille filtrable), réalisations,
 * révélation de l'arbre, appel.
 *
 * Ce qui change : la matière (vert / bois au lieu de noir / or) et les sources
 * de contenu — ici le personnalisateur et les types Prestations / Réalisations.
 * Aucun texte client, aucun chiffre, aucune photo n'est écrit en dur : un
 * réglage vide masque son bloc.
 *
 * Sans JavaScript, sans GSAP ou sous « réduire les animations », la page
 * s'affiche dans son état final : tout est visible et cliquable.
 *
 * @package LA_Environnement
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();

$dir = get_template_directory_uri();

/* Médias (personnalisateur). Chaque champ vide laisse le dégradé CSS en place. */
$lae_hero_img   = lae_reglage( 'hero_image' );
$lae_hero_video = lae_reglage( 'cine_video' );
$lae_hero_post  = lae_reglage( 'cine_poster' );
$lae_tab_img    = lae_reglage( 'cine_tab_image' );
$lae_scene_img  = lae_reglage( 'cine_scene_image' );
$lae_main_img   = lae_reglage( 'cine_main_image' );
$lae_arbre_img  = lae_reglage( 'cine_arbre_image' );
$lae_bois_img   = lae_reglage( 'cine_matiere_image' );

$lae_tel        = lae_reglage( 'telephone' );
$lae_tel_lien   = lae_tel_lien();
$lae_contact    = lae_url_contact();
?>
<style>

  :root{
    --feuille:#7fb04a; --feuille-hi:#a9d36a; --ink:#04140c; --panel:#0a1811;
    --text:#eef4ee; --muted:#93a396;
    --serif:Georgia,"Times New Roman","Liberation Serif",serif;
    --sans:system-ui,-apple-system,"Segoe UI",Roboto,Helvetica,Arial,sans-serif;
  }
  .lae-cine *,.lae-cine *::before,.lae-cine *::after{box-sizing:border-box;margin:0;padding:0}
  html{scroll-behavior:auto}
  body.home{background:var(--ink);color:var(--text);font-family:var(--sans);-webkit-font-smoothing:antialiased;overflow-x:hidden}
  .lae-cine img{display:block;max-width:100%}
  .lae-cine a{color:inherit}
  .wrap{max-width:1240px;margin:0 auto;padding:0 28px}
  .eyebrow{font-size:.72rem;letter-spacing:.34em;text-transform:uppercase;color:var(--feuille);font-weight:700}
  .stitle{font-family:var(--serif);font-weight:500;font-size:clamp(1.9rem,5vw,3.4rem);line-height:1.06;letter-spacing:-.01em}
  .stitle em{font-style:italic;color:var(--feuille-hi)}
  .lead{color:var(--muted);font-size:clamp(1rem,2.2vw,1.12rem);line-height:1.65;max-width:56ch}
  /* Boutons v2 : biseau, plaque gravee. Meme construction que .ag-btn-gold
     dans main.css — la page cinema porte son propre CSS, elle ne peut pas en
     heriter, mais elle ne doit pas non plus inventer une deuxieme forme.
     Aucune ombre : la profondeur vient du cadre grave. */
  .btn{--c:9px;position:relative;isolation:isolate;display:inline-flex;align-items:center;gap:14px;
       min-height:52px;padding:0 30px;background:transparent;color:#0d1a06;font-weight:800;
       text-decoration:none;border-radius:0;box-shadow:none;font-size:.98rem}
  .btn::before{content:"";position:absolute;inset:0;z-index:-1;
       background:linear-gradient(120deg,var(--feuille),var(--feuille-hi));
       clip-path:polygon(var(--c) 0,100% 0,100% calc(100% - var(--c)),calc(100% - var(--c)) 100%,0 100%,0 var(--c))}
  .btn::after{content:"";position:absolute;inset:0;z-index:-2;border:1.5px solid var(--feuille);
       clip-path:polygon(var(--c) 0,100% 0,100% calc(100% - var(--c)),calc(100% - var(--c)) 100%,0 100%,0 var(--c));
       transform:translate(7px,7px);opacity:.7;
       transition:transform .45s cubic-bezier(.2,.8,.2,1),opacity .45s cubic-bezier(.2,.8,.2,1)}
  .btn:hover::after,.btn:focus-visible::after{transform:translate(0,0);opacity:1}
  .btn:active{transform:scale(.98)}
  .btn:focus-visible{outline:2px solid var(--feuille-hi);outline-offset:4px}
  .btn .ag-l{display:inline-block;transition:transform .45s cubic-bezier(.2,.8,.2,1)}
  .btn:hover .ag-l{transform:translateX(-2px)}
  .ag-trait{display:block;flex:none;width:34px;height:14px;overflow:visible}
  .ag-trait line{stroke:currentColor;stroke-width:2;stroke-linecap:round;transform-origin:left center;
       transform:scaleX(.55);transition:transform .45s cubic-bezier(.2,.8,.2,1)}
  .ag-trait polyline{fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;
       transform:translateX(-14px);transition:transform .45s cubic-bezier(.2,.8,.2,1)}
  .btn:hover .ag-trait line{transform:scaleX(1)}
  .btn:hover .ag-trait polyline{transform:none}
  .btn--ghost{color:var(--text);font-weight:600}
  .btn--ghost::before{background:transparent;border:1px solid rgba(255,255,255,.22)}
  .btn--ghost::after{border-color:rgba(127,176,74,.55)}
  .btn--ghost:hover::before{border-color:var(--feuille)}

  /* ---------- HERO ---------- */
  .hero{position:relative;height:100svh;overflow:hidden;display:flex;align-items:flex-end}
  .hero__bg{position:absolute;inset:-6% 0 0;z-index:0}
  .hero__bg img{width:100%;height:112%;object-fit:cover;object-position:center 45%}
  /* Ordre des couches : baie (0) < egerie (1) < voile (2) < texte (3).
     L'egerie passe SOUS le voile, sinon le texte se poserait a meme la photo
     et deviendrait illisible. */
  .hero__veil{position:absolute;inset:0;z-index:2;
    background:linear-gradient(180deg,rgba(4,20,12,.78),rgba(4,20,12,.12) 34%,rgba(4,20,12,.45) 68%,rgba(4,20,12,.97)),
               radial-gradient(120% 80% at 22% 60%,transparent 38%,rgba(4,20,12,.62))}
  /* PLEIN ECRAN (03/09) : l'egerie occupait une colonne de 46vw collee a
     droite, et object-fit:cover y recadrait horizontalement — le second visage
     sortait du cadre sur grand ecran. Elle prend maintenant tout le hero, donc
     le recadrage n'a plus qu'une variable a regler : la hauteur. Le masque en
     degrade n'a plus de raison d'etre, il ne fondait qu'un bord de colonne. */
  .hero__eg{position:absolute;z-index:1;inset:0;width:100%;height:100%;pointer-events:none;
    transform-origin:50% 40%}
  .hero__eg video,.hero__eg img{position:absolute;inset:0;width:100%;height:100%;
    object-fit:cover;object-position:center 32%}
  .hero__eg video{z-index:1}
  .hero__in{position:relative;z-index:3;width:100%;padding-bottom:clamp(52px,10vh,120px)}
  /* Tant que la barre cookies est affichee, le contenu du hero remonte de sa
     hauteur : le prix et le bouton d'appel doivent rester visibles, ce sont
     eux qui font vendre. `:has()` evite d'ajouter une classe en JavaScript ;
     sur un navigateur qui ne le connait pas, la barre se contente de se poser
     par-dessus comme avant — rien ne casse. */
  body:has(.ag-cookie:not([hidden])) .hero__in{padding-bottom:calc(clamp(52px,10vh,120px) + 70px)}
  .hero__t{font-family:var(--serif);font-weight:500;line-height:.97;font-size:clamp(2.9rem,8.4vw,7rem);letter-spacing:-.022em;margin:.18em 0 0}
  .hero__t em{font-style:italic;color:var(--feuille-hi)}
  .w{display:inline-block;overflow:hidden;vertical-align:bottom;padding-bottom:.18em;margin-bottom:-.18em}
  .w i{display:inline-block;font-style:inherit}
  .hero__t .w{display:inline-block;overflow:hidden;vertical-align:bottom;padding-bottom:.18em;margin-bottom:-.18em}
  .hero__t .w i{display:inline-block;font-style:inherit}
  .hero__sub{margin-top:20px;max-width:44ch;color:#cbd8cd;font-size:clamp(1rem,2.2vw,1.14rem);line-height:1.6}
  /* Preuve du premier écran : le visage de la personne qui fait le travail,
     et trois clients réels. Jamais animée (Règle du Héros Immobile). */
  .hero__proof{margin-top:20px;display:flex;align-items:center;gap:13px;max-width:44ch}
  .hero__proof img{width:46px;height:46px;flex:0 0 auto;border-radius:50%;object-fit:cover;object-position:center 20%;
       border:1px solid rgba(127,176,74,.45)}
  .hero__proof span{font-size:.86rem;line-height:1.45;color:var(--muted)}
  .hero__proof b{display:block;color:#fff;font-weight:600;font-size:.92rem}
  .hero__cta{margin-top:26px;display:flex;gap:14px;flex-wrap:wrap}
  .btn--tel{display:inline-flex;align-items:center;gap:9px}
  .btn--tel svg{width:17px;height:17px;flex:0 0 auto}
  .hero__scroll{position:absolute;left:50%;bottom:16px;transform:translateX(-50%);z-index:4;font-size:.68rem;
    letter-spacing:.32em;color:rgba(255,255,255,.5);animation:bob 2.2s ease-in-out infinite}
  @keyframes bob{0%,100%{transform:translate(-50%,0)}50%{transform:translate(-50%,7px)}}
  .feuillemark{position:absolute;z-index:2;left:50%;top:42%;transform:translate(-50%,-50%);width:min(62vw,600px);opacity:.10;mix-blend-mode:screen}

  /* ---------- MARQUEE ---------- */
  .mq{position:relative;border-top:1px solid rgba(127,176,74,.22);border-bottom:1px solid rgba(127,176,74,.22);
      overflow:hidden;white-space:nowrap;padding:22px 0;background:#061a10}
  .mq__bg{position:absolute;inset:0;opacity:.5}
  .mq__bg img{width:100%;height:100%;object-fit:cover}
  .mq__in{position:relative;display:inline-flex;gap:46px;will-change:transform}
  .mq__in span{font-family:var(--serif);font-size:clamp(1.1rem,2.4vw,1.7rem);color:#eef3e4}
  .mq__in b{color:var(--feuille)}

  /* ---------- TABLEAU ---------- */
  .tab{position:relative;height:230svh}
  .tab__stick{position:sticky;top:0;height:100svh;overflow:hidden;display:grid;place-items:center}
  .tab__img{position:absolute;inset:0}
  .tab__img img{width:100%;height:100%;object-fit:cover;transform:scale(1.12)}
  .tab__veil{position:absolute;inset:0;background:linear-gradient(180deg,rgba(4,20,12,.62),rgba(4,20,12,.25) 40%,rgba(4,20,12,.9))}
  .tab__cap{position:relative;z-index:2;text-align:center;padding:0 26px;max-width:900px}
  .tab__cap h2{font-family:var(--serif);font-weight:500;font-size:clamp(2rem,6.4vw,4.6rem);line-height:1.02}
  .tab__cap h2 em{font-style:italic;color:var(--feuille-hi)}
  .tab__cap p{margin:20px auto 0;color:#d5ded6;max-width:52ch;line-height:1.7}

  /* ---------- CHAPITRES ---------- */
  /* overflow-x:clip (et non hidden) : aucune animation latérale ne peut plus
     faire défiler la PAGE de côté, sans créer pour autant un conteneur de
     défilement ni casser un position:sticky descendant. */
  .chs{padding:clamp(50px,7vh,90px) 0;overflow-x:clip}
  .ch{display:grid;grid-template-columns:1fr 1fr;gap:clamp(24px,5vw,70px);align-items:center;margin-bottom:clamp(28px,5vh,64px)}
  .ch:nth-child(even) .ch__txt{order:2}
  .ch__media{position:relative;overflow:hidden;aspect-ratio:4/3;border:1px solid rgba(127,176,74,.2);will-change:transform}
  .ch__media img{width:100%;height:100%;object-fit:cover;transform:scale(1.16)}
  .ch__n{font-family:var(--serif);font-size:clamp(2.4rem,6vw,4.4rem);color:var(--feuille);opacity:.8;line-height:1;will-change:transform}
  .ch__t{font-family:var(--serif);font-weight:500;font-size:clamp(1.8rem,4.4vw,3.1rem);line-height:1.06;margin:.2em 0 .5em}
  .ch__t em{font-style:italic;color:var(--feuille-hi)}
  .ch__p{color:var(--muted);font-size:1.05rem;line-height:1.75;max-width:46ch}
  .ch__meta{margin-top:24px;display:flex;gap:24px;flex-wrap:wrap;font-size:.76rem;letter-spacing:.18em;text-transform:uppercase;color:var(--feuille)}

  /* ---------- DISSOLUTION ---------- */
  .ds{position:relative;height:620svh}
  .ds__stick{position:sticky;top:0;height:100svh;overflow:hidden;display:grid;place-items:center}
  #cv{width:100%;height:100%;display:block}
  .ds__photo{display:none;position:absolute;inset:0;width:100%;height:100%;object-fit:cover;z-index:1}
  .ds__hand{position:absolute;z-index:4;width:min(52vw,620px);mix-blend-mode:screen;opacity:0;pointer-events:none}
  .ds__veil{position:absolute;inset:0;z-index:3;background:linear-gradient(180deg,rgba(4,20,12,.42),transparent 32%,rgba(4,20,12,.78))}
  .ds__cap{position:absolute;z-index:5;left:0;right:0;bottom:11vh;text-align:center;padding:0 26px}
  .ds__cap h2{font-family:var(--serif);font-weight:500;font-size:clamp(1.9rem,5.4vw,4rem);line-height:1.05}
  .ds__cap h2 em{font-style:italic;color:var(--feuille-hi)}
  .ds__cap p{margin:16px auto 0;max-width:52ch;color:var(--muted);line-height:1.65}

  /* ---------- OFFRES (couche de la scène) ---------- */
  .of{position:absolute;inset:0;z-index:6;display:flex;flex-direction:column;justify-content:center;
      padding:clamp(84px,12vh,120px) 0 clamp(20px,4vh,50px);overflow:hidden;pointer-events:none}
  .of.is-live{pointer-events:auto}
  .of__bg{position:absolute;inset:0;opacity:0}
  .of__bg img{width:100%;height:100%;object-fit:cover}
  .of__in{position:relative;z-index:2;text-align:center;width:100%}
  .of__grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:20px;margin:26px 0 18px;text-align:left}
  @media(max-width:900px){.of__grid{grid-template-columns:1fr}}
  .pack{position:relative;background:#0b1d13;border:1px solid rgba(255,255,255,.09);border-radius:18px;overflow:hidden;
        transform-style:preserve-3d;transition:border-color .35s,box-shadow .35s}
  .pack:hover{border-color:rgba(127,176,74,.6);box-shadow:0 40px 90px -50px rgba(127,176,74,.8)}
  .pack.star{border-color:rgba(127,176,74,.55);box-shadow:0 34px 80px -46px rgba(127,176,74,.7)}
  .pack img{width:100%;aspect-ratio:4/3;object-fit:cover}
  .pack__body{padding:22px 24px 26px}
  .pack__cta{display:block;text-align:center;margin-top:6px}
  /* Le contenu du pack en TEXTE : masqué sur PC (le JPEG le porte), affiché
     sur téléphone où l'image est retirée. Même information des deux côtés. */
  .pack__txt{display:none}
  .of__note{position:relative;z-index:2;color:var(--muted);font-size:.95rem}
  .of__note a{color:var(--feuille-hi)}

  /* ---------- ATELIER (couche de la scène) ---------- */
  /* L'atelier n'est masqué QUE si le JS tourne (classe .js-cine posée en tête
     de page). Sans JS, il reste visible : DESIGN.md interdit de laisser une
     section à opacity:0 en attendant un script. */
  .js-cine .at{opacity:0}
  .at{position:absolute;inset:0;z-index:7;display:flex;flex-direction:column;justify-content:center;
      padding:clamp(84px,12vh,120px) 0 clamp(18px,3vh,40px);pointer-events:none;overflow:hidden}
  .at .stitle{font-size:clamp(1.4rem,3.2vw,2.2rem);margin:8px 0 10px}
  .at .lead{font-size:clamp(.9rem,1.7vw,1rem);line-height:1.5}
  .at .at__head{margin:0 auto 14px}
  @media(max-height:900px){.at .card__d{display:none}.at .card__t{font-size:.92rem}.at .lead{display:none}}
  .at.is-live{pointer-events:auto}
  .at .wrap{position:relative;z-index:2;width:100%}
  .at__head{text-align:center;max-width:760px;margin:0 auto 18px}
  .at__filters{display:flex;gap:10px;justify-content:center;flex-wrap:wrap;margin-bottom:18px}
  .fbtn{background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.14);color:#c9d6cb;border-radius:999px;
        padding:9px 20px;font-size:.85rem;cursor:pointer;transition:.25s;font-family:inherit}
  .fbtn:hover{border-color:var(--feuille)}
  .fbtn.is-on{background:linear-gradient(120deg,var(--feuille),var(--feuille-hi));color:#0d1a06;font-weight:700;border-color:transparent}
  .at__grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:16px}
  @media(max-width:1100px){.at__grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
  @media(max-width:640px){.at__grid{grid-template-columns:1fr}}
  .card{position:relative;background:#0b1d13;border:1px solid rgba(255,255,255,.08);border-radius:16px;overflow:hidden;
        text-decoration:none;display:block;transition:border-color .35s,transform .35s}
  .card:hover{border-color:rgba(127,176,74,.55);transform:translateY(-4px)}
  .card__media{position:relative;aspect-ratio:16/10;overflow:hidden}
  .card__media img{width:100%;height:100%;object-fit:cover;transition:transform .7s cubic-bezier(.2,.7,.2,1)}
  .card:hover .card__media img{transform:scale(1.07)}
  .card__badge{position:absolute;top:12px;left:12px;z-index:2;background:rgba(4,20,12,.72);border:1px solid rgba(127,176,74,.5);
    color:var(--feuille-hi);font-size:.68rem;letter-spacing:.12em;text-transform:uppercase;padding:5px 12px;border-radius:999px}
  .card__body{padding:13px 15px 15px}
  .card__t{font-size:1.02rem;font-weight:700;margin-bottom:6px}
  .card__d{color:var(--muted);font-size:.84rem;line-height:1.45;min-height:2.6em}
  .card__go{margin-top:12px;font-size:.82rem;color:var(--feuille);letter-spacing:.06em}


  /* ---------- RÉALISATIONS (aperçu) ---------- */
  .rz{padding:clamp(70px,12vh,150px) 0;text-align:center;position:relative;overflow:hidden}
  .rz__glow{position:absolute;left:50%;top:34%;transform:translate(-50%,-50%);width:min(94vw,1200px);height:64vh;
    background:radial-gradient(closest-side,rgba(127,176,74,.12),transparent 72%);pointer-events:none}
  .rz__in{position:relative;z-index:2}
  .rz__grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:22px;margin:38px 0 34px;text-align:left}
  .rz__card{background:#0a1811;border:1px solid rgba(255,255,255,.09);border-radius:18px;overflow:hidden;
    display:flex;flex-direction:column;transition:border-color .35s,transform .35s,box-shadow .35s}
  .rz__card:hover{border-color:rgba(127,176,74,.55);transform:translateY(-5px);box-shadow:0 50px 100px -55px rgba(127,176,74,.7)}
  .rz__vue{display:block;position:relative;overflow:hidden;aspect-ratio:16/10;background:#061a10}
  .rz__vue img{width:100%;height:100%;object-fit:cover;object-position:center top;transition:transform .8s cubic-bezier(.2,.7,.2,1)}
  .rz__card:hover .rz__vue img{transform:scale(1.06)}
  .rz__body{padding:18px 20px 22px;display:flex;flex-direction:column;gap:9px;flex:1}
  .rz__meta{font-size:.68rem;letter-spacing:.2em;text-transform:uppercase;color:var(--feuille)}
  .rz__card h3{font-family:var(--serif);font-weight:500;font-size:1.42rem;line-height:1.1;margin:0}
  .rz__card p{color:var(--muted);font-size:.92rem;line-height:1.6;margin:0}
  .rz__pts{list-style:none;display:flex;flex-wrap:wrap;gap:7px;margin:2px 0 0;padding:0}
  .rz__pts li{border:1px solid rgba(127,176,74,.3);border-radius:999px;padding:5px 12px;font-size:.72rem;color:#c9d6cb}
  .rz__liens{margin-top:auto;padding-top:14px;display:flex;flex-wrap:wrap;gap:8px 16px;font-size:.84rem}
  .rz__liens a{text-decoration:none;color:var(--feuille-hi);border-bottom:1px solid transparent;transition:border-color .25s}
  .rz__liens a:hover{border-color:var(--feuille-hi)}
  .rz__liens .rz__avis{color:#c9d6cb}

  /* ---------- AVIS GOOGLE ---------- */
  .av{max-width:1180px;margin:38px auto 32px}
  .av__head{display:flex;align-items:center;justify-content:center;gap:12px;flex-wrap:wrap;margin-bottom:22px}
  .av__g{display:inline-flex;background:#fff;border-radius:50%;padding:7px;line-height:0}
  .av__rate{font-size:.95rem;color:#c9d6cb}
  .av__rate strong{font-family:var(--serif);font-size:1.6rem;color:var(--feuille-hi);margin-right:8px;vertical-align:-2px}
  .av__stars{color:var(--feuille);letter-spacing:.1em;margin-right:6px}
  .av__grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:18px;text-align:left}
  .av__card{background:rgba(255,255,255,.035);border:1px solid rgba(255,255,255,.09);border-radius:16px;padding:20px 20px 18px;
    display:flex;flex-direction:column;transition:border-color .35s}
  .av__card:hover{border-color:rgba(127,176,74,.45)}
  .av__top{display:flex;align-items:center;gap:12px;margin-bottom:12px}
  .av__ava{width:42px;height:42px;flex:none;border-radius:50%;display:grid;place-items:center;overflow:hidden;
    background:linear-gradient(120deg,var(--feuille),var(--feuille-hi));color:#0d1a06;font-weight:800;font-size:1.05rem}
  .av__ava img{width:100%;height:100%;object-fit:cover}
  .av__who{display:flex;flex-direction:column;min-width:0}
  .av__nom{font-weight:700;font-size:.95rem}
  .av__role{font-size:.75rem;color:var(--muted)}
  .av__q{color:#d5ded6;font-size:.93rem;line-height:1.65;margin:0}
  .av__lien{margin-top:auto;padding-top:14px;font-size:.82rem;color:var(--feuille-hi);text-decoration:none}
  .av__lien:hover{text-decoration:underline}

  /* ---------- LION + « REFAIS MON SITE » ---------- */
  /* Le lion reste au centre de l'animation : il devient le medaillon de fond
     sur lequel se pose l'outil phare. Un voile radial garantit la lisibilite
     du texte par-dessus la photo. */
  .arbre{position:relative;height:210svh}
  .arbre__stick{position:sticky;top:0;height:100svh;display:grid;place-items:center;overflow:hidden;
    background:radial-gradient(60% 60% at 50% 45%,#0f2418,#04140c)}
  .arbre__img{position:absolute;z-index:1;width:min(58vw,520px);opacity:0;border-radius:50%;
    box-shadow:0 0 0 1px rgba(127,176,74,.5),0 70px 150px -50px rgba(127,176,74,.55)}
  .arbre__veil{position:absolute;inset:0;z-index:2;pointer-events:none;
    background:radial-gradient(52% 52% at 50% 50%,rgba(4,20,12,.5),rgba(4,20,12,.68) 68%,rgba(4,20,12,.8))}
  .arbre__panel{position:relative;z-index:3;width:100%;max-width:720px;padding:0 28px;text-align:center}
  .arbre__panel .eyebrow{display:inline-block;margin-bottom:14px}
  .arbre__lead{color:var(--muted);font-size:clamp(.96rem,2.1vw,1.08rem);line-height:1.62;
    max-width:54ch;margin:16px auto 0}
  .arbre__w{position:absolute;z-index:3;bottom:6vh;font-size:clamp(.62rem,1.4vw,.84rem);letter-spacing:.7em;
    color:var(--feuille);text-transform:uppercase;opacity:.75}


  /* ---------- CTA + PIED ---------- */
  .cta{padding:clamp(80px,15vh,170px) 0;text-align:center}
  .cta p{color:var(--muted);margin:18px auto 32px;max-width:48ch;line-height:1.7}
  .ft{border-top:1px solid rgba(255,255,255,.08);padding:38px 0 56px;color:#5d6d61;font-size:.82rem;
      display:flex;justify-content:space-between;gap:16px;flex-wrap:wrap}
  .ft a{color:#889489;text-decoration:none}
  .ft a:hover{color:var(--feuille)}

  @media(max-width:820px){
    .ch{grid-template-columns:1fr}
    .ch:nth-child(even) .ch__txt{order:0}
    .hero__eg{opacity:.95}
  }

  /* ======================================================
     TABLETTE ET MOBILE — les scènes épinglées deviennent
     un enchaînement classique, tout tient dans le cadre.
     ====================================================== */
  @media(max-width:1100px){
    .rz__grid{grid-template-columns:repeat(2,minmax(0,1fr))}
    .av__grid{grid-template-columns:1fr}
    .at__grid{grid-template-columns:repeat(2,minmax(0,1fr))}
    .of__grid{grid-template-columns:repeat(3,minmax(0,1fr));gap:14px}
    .ch{gap:26px}
  }
  @media(max-width:960px){
    .wrap{padding:0 20px}

    .hero{height:92svh;align-items:flex-end}
    /* Telephone : plein ecran aussi. On remonte le point focal, les visages
       sont dans le tiers haut et le texte occupe le bas. */
    .hero__eg video,.hero__eg img{object-position:center 26%}
    .hero__in{padding-bottom:30px;position:relative;isolation:isolate}
    .hero__in::before{content:"";position:absolute;inset:-40px -24px -60px;z-index:-1;pointer-events:none;
      background:linear-gradient(0deg,rgba(4,20,12,.95) 34%,rgba(4,20,12,.78) 62%,rgba(4,20,12,0) 100%)}
    .hero__t{font-size:clamp(2.3rem,11vw,3.6rem);line-height:1.02}
    .hero__sub{font-size:1rem;max-width:34ch}
    .hero__proof{margin-top:16px;gap:11px;max-width:34ch}
    .hero__proof img{width:42px;height:42px}
    .hero__proof span{font-size:.8rem}
    .hero__proof b{font-size:.86rem}
    /* Deux boutons pleine largeur : un pouce sur un chantier ne vise pas. */
    .hero__cta{margin-top:20px;gap:10px}
    .hero__cta .btn{flex:1 1 100%;justify-content:center;text-align:center}
    .btn{min-height:48px;padding:0 22px;font-size:.92rem}
    .btn::after{transform:translate(5px,5px)}
    .feuillemark{width:92vw;opacity:.08;top:38%}
    .hero__scroll{display:none}

    .mq{padding:14px 0}
    .mq__in{gap:28px}
    .mq__in span{font-size:1.05rem}

    .tab{height:150svh}
    /* la peinture est en 16/9 : on la montre entière, tableau puis légende */
    .tab__stick{display:flex;flex-direction:column;justify-content:center;gap:clamp(18px,4svh,44px)}
    .tab__img{position:relative;inset:auto;width:100%;height:auto;aspect-ratio:16/9}
    .tab__img img{object-fit:cover;height:100%;transform:none!important}
    .tab__veil{display:none}
    .tab__cap{align-self:auto;padding:0 20px}
    .tab__cap h2{font-size:clamp(1.7rem,7.4vw,2.4rem)}
    .tab__cap p{font-size:.95rem;margin-top:12px}

    .chs{padding:44px 0}
    .ch{grid-template-columns:1fr;margin-bottom:44px}
    .ch:nth-child(even) .ch__txt{order:0}
    .ch__media{aspect-ratio:4/3}
    .ch__p{font-size:1rem}
    .ch__meta{gap:16px;font-size:.7rem}

    /* ── LA SCÈNE RESTE ÉPINGLÉE : les choses sortent de la paume ──
       Le canvas de dissolution (coûteux) est remplacé par une photo qui
       se dissout en flou.
       03/09 : les offres ne sortent plus UNE PAR UNE. Chaque carte était
       détruite avant l'arrivée de la suivante, donc les trois prix
       n'existaient jamais ensemble : impossible de comparer 490 / 890 / 1490,
       alors que c'est exactement la décision que le visiteur vient prendre.
       Elles jaillissent désormais toutes les trois et RESTENT jusqu'à la
       sortie de la scène. Pour qu'elles tiennent dans le cadre, l'image du
       pack est masquée sur téléphone et remplacée par son contenu en texte. */
    .ds{height:560svh}
    #cv{display:none}
    .ds__photo{display:block}
    .ds__hand{left:0;right:0;margin:0 auto;top:2svh;width:min(96vw,440px)}
    .ds__cap{bottom:9svh;padding:0 20px}
    .ds__cap h2{font-size:clamp(1.7rem,7.4vw,2.4rem)}
    .ds__cap p{font-size:.94rem;margin-top:12px}

    .of,.at{padding:0}
    .of__in{display:flex;flex-direction:column;justify-content:center;height:100%}
    .of .lead{display:none}
    .of__bg{opacity:.3}
    /* Les 3 packs empilés, visibles ENSEMBLE et comparables d'un coup d'œil.
       flex:0 0 auto est indispensable : .of__in est une colonne flex de
       hauteur 100%, et sans ça la grille se faisait comprimer par flexbox —
       chaque carte etait alors rognee par son overflow:hidden, delai et
       bouton coupes (constate a 375x667 le 03/09). */
    .of__grid{display:grid;grid-template-columns:1fr;gap:9px;position:relative;height:auto;margin:12px 0 0;
        flex:0 0 auto}
    .pack{position:relative;inset:auto;margin:0;width:auto;border-radius:14px}
    .pack img{display:none}
    .pack__body{padding:12px 14px 13px}
    .pack__txt{display:block}
    .pack__nom{font-family:var(--sans);font-size:.98rem;font-weight:700;color:#fff;line-height:1.2;
        display:flex;align-items:baseline;justify-content:space-between;gap:10px}
    .pack__prix{font-family:var(--serif);font-style:italic;font-size:1.22rem;color:var(--feuille-hi);white-space:nowrap}
    .pack__feats{list-style:none;margin:7px 0 0;padding:0;display:grid;gap:3px}
    .pack__feats li{position:relative;padding-left:15px;font-size:.79rem;line-height:1.35;color:var(--muted)}
    .pack__feats li::before{content:"";position:absolute;left:0;top:.52em;width:6px;height:6px;border-radius:50%;
        background:var(--feuille);opacity:.75}
    .pack__delai{margin-top:7px;font-size:.74rem;letter-spacing:.02em;color:var(--feuille)}
    /* La maintenance décide un patron de TPE : elle était masquée
       précisément sur l'appareil où il la lit. */
    .of__note{display:block;margin-top:11px;font-size:.78rem;line-height:1.4}
    .pack__cta{padding:11px 16px;font-size:.84rem;margin-top:10px}
    /* Écran court (iPhone SE : 667 px de haut, encore très répandu).
       Trois cartes complètes + le titre + la note ne tiennent pas en
       563 px utiles : on retire d'abord le décor, puis le 3e argument.
       Nom, prix, délai et bouton ne partent JAMAIS — ce sont eux qui
       permettent de comparer et d'acheter. */
    @media(max-height:760px){
      .of .eyebrow{display:none}
      .of .stitle{font-size:clamp(1.35rem,5.6vw,1.85rem)}
      .of__grid{gap:6px;margin-top:8px}
      .pack{border-radius:12px}
      .pack__body{padding:9px 12px 10px}
      .pack__nom{font-size:.94rem}
      .pack__prix{font-size:1.14rem}
      .pack__feats li:nth-child(3){display:none}
      .pack__feats li{font-size:.75rem}
      .pack__delai{margin-top:5px;font-size:.72rem}
      .pack__cta{padding:9px 14px;font-size:.8rem;margin-top:8px}
      .of__note{margin-top:7px;font-size:.72rem}
    }

    .at .wrap{display:flex;flex-direction:column;justify-content:center;height:100%}
    .at .lead{display:none}
    .at .stitle{font-size:clamp(1.5rem,6.2vw,2rem);margin:8px 0 0}
    .at__head{margin:0 auto 12px}
    .at__filters{margin-bottom:12px;gap:7px}
    .fbtn{padding:7px 14px;font-size:.78rem}
    .at__grid{grid-template-columns:repeat(2,minmax(0,1fr));gap:10px}
    .at .card__media{aspect-ratio:16/9}
    .at .card__d,.at .card__go{display:none}
    .at .card__t{font-size:.86rem;margin:0}
    .card__body{padding:9px 11px 11px}
    .card__badge{font-size:.6rem;padding:4px 9px;top:8px;left:8px}

    .rz{padding:56px 0}
    .rz__grid{grid-template-columns:1fr;gap:16px;margin:26px 0 24px}
    .av{margin:26px auto 24px}
    .av__grid{grid-template-columns:1fr;gap:14px}
    .rz__card h3{font-size:1.28rem}

    .arbre{height:150svh}
    .arbre__img{width:min(86vw,400px)}
    .arbre__w{bottom:4vh;letter-spacing:.4em}
    .cta{padding:60px 0}
    .ft{flex-direction:column;align-items:center;text-align:center;gap:8px}
  }
  @media(max-width:600px){
    .mq__in span{font-size:.95rem}
    .tab{height:140svh}
  }
  /* tablette : la scène est épinglée comme en desktop, mais les 3 offres
     tiennent côte à côte — inutile de les montrer une par une. */
  @media(min-width:701px) and (max-width:960px){
    .ds__hand{width:min(62vw,520px);top:3svh}
    .of__grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px;
              height:auto;position:static;margin:22px 0 0}
    .pack{position:relative;inset:auto;margin:0;width:auto;height:auto}
    .of .lead{display:block;margin:14px auto 0}
    .at .lead{display:block;margin:0 auto}
    .at .card__t{font-size:.95rem}
    .card__body{padding:12px 14px 14px}
  }

  /* écrans courts : on resserre encore la grille de l'atelier */
  @media(max-width:960px) and (max-height:760px){
    .ds__hand{width:min(78vw,340px)}
    .of__grid{height:min(44svh,300px)}
    .pack{width:min(70vw,260px)}
    .at__grid{gap:8px}
    .at .card__t{font-size:.78rem}
    .card__body{padding:7px 9px 9px}
  }
  @media(prefers-reduced-motion:reduce){*{animation:none!important;transition:none!important}}

  /* ==========================================================
     L'accueil est sombre : l'en-tête et la barre mobile du thème
     s'y adaptent, plutôt que d'ouvrir un deuxième en-tête rien
     que pour cette page.
     ========================================================== */
  .home .lae-header{position:fixed;left:0;right:0;background:transparent;backdrop-filter:none;
    border-bottom:1px solid transparent;transition:background .4s ease,border-color .4s ease,padding .4s ease}
  .home .lae-header.is-stuck{background:rgba(4,20,12,.78);backdrop-filter:saturate(150%) blur(12px);
    border-bottom-color:rgba(127,176,74,.2)}
  .home .lae-marque{color:#eef4ee}
  .home .lae-marque__glyphe{color:var(--feuille-hi)}
  .home .lae-marque__baseline{color:#93a396}
  .home .lae-nav a{color:#c9d6cb}
  .home .lae-nav a:hover,.home .lae-nav .current-menu-item>a{background:rgba(127,176,74,.16);color:var(--feuille-hi)}
  .home .lae-burger{border-color:rgba(255,255,255,.24);color:#eef4ee}
  @media(max-width:900px){.home .lae-nav{background:#0a1811;border-bottom-color:rgba(127,176,74,.2)}}
  .home .lae-main{padding:0}
  /* Repli des scènes quand aucune photo n'est encore fournie : de la matière,
     jamais un rectangle noir ni une image cassée. */
  .hero--nu::after{background:
      radial-gradient(90% 80% at 82% 12%,rgba(127,176,74,.5),transparent 62%),
      linear-gradient(160deg,#0f2418 0%,#04140c 62%,#020c07 100%)}
  .tab__img--nu{background:linear-gradient(150deg,rgba(18,48,30,.3),rgba(4,20,12,.55) 70%)}
  .ds__stick{background:radial-gradient(70% 70% at 50% 40%,#0f2418,#04140c)}
  .arbre__img--nu{background:radial-gradient(circle at 38% 32%,#3f7a3f,#0f2418 68%);aspect-ratio:1;
    width:min(58vw,520px)}

  /* ==========================================================
     Cartes et scènes sans photo : de la matière et de la
     typographie prennent la place, jamais un trou noir.
     Ces règles disparaissent d'elles-mêmes dès qu'une image
     mise en avant est renseignée.
     ========================================================== */
  .card__media--nu{background:linear-gradient(150deg,rgba(18,48,30,.6),rgba(4,20,12,.8));display:grid;place-items:center}
  .card__glyphe{color:var(--feuille-hi);opacity:.85}
  .card__glyphe svg{width:44px;height:44px}
  .ch__media--nu{background:linear-gradient(150deg,rgba(18,48,30,.55),rgba(4,20,12,.75))}
  .rz__vue--nu{background:linear-gradient(150deg,rgba(18,48,30,.6),rgba(4,20,12,.8))}
  .pack--nu .pack__txt{display:block}
  /* Le nom colle à son icône : la règle d'Alliance Groupe écartait les deux
     bouts (justify-content:space-between) parce qu'un prix fermait la ligne.
     Ici il n'y a pas de prix — sans cette remise à zéro, le titre part à droite. */
  .pack__nom{font-family:var(--sans);font-size:1.06rem;font-weight:700;color:#fff;line-height:1.3;
    display:flex;align-items:center;justify-content:flex-start;gap:2px}
  .pack__ic{display:inline-grid;place-items:center;width:34px;height:34px;flex:none;margin-right:9px;
    border-radius:10px;background:rgba(127,176,74,.16);color:var(--feuille-hi)}
  .pack__ic svg{width:19px;height:19px}
  .pack__res{margin-top:9px;color:var(--muted);font-size:.89rem;line-height:1.55}
  .pack__delai{margin-top:9px;font-size:.72rem;letter-spacing:.14em;text-transform:uppercase;color:var(--feuille)}
  /* Chez Alliance Groupe la carte porte une image et le bouton est seul dans
     le corps : `display:block` suffisait. Ici le corps contient du texte, et
     un bouton en flex est le seul moyen que son libellé reste centré dans sa
     plaque au lieu de remonter sur la ligne précédente. */
  .pack__cta{display:flex;align-items:center;justify-content:center;margin-top:16px}
  .ds__photo--nu{background:linear-gradient(155deg,#12301e,#04140c 68%)}
  .ds__hand--nu{aspect-ratio:4/3;border-radius:50%;
    background:radial-gradient(circle at 50% 46%,rgba(127,176,74,.55),rgba(15,36,24,0) 66%)}

  /* ==========================================================
     LA COLONNE — le décor unique de la page.
     Une vidéo qui tient tout l'écran, et un arbre de plusieurs
     écrans de haut que l'on descend en défilant : cime en haut
     de page, racines en bas. Tout le reste passe par-dessus.
     ========================================================== */
  .lae-colonne{position:fixed;inset:0;z-index:-1;overflow:hidden;pointer-events:none;background:var(--ink)}
  .lae-colonne__video{position:absolute;inset:0}
  .lae-colonne__video video,.lae-colonne__video img{width:100%;height:100%;object-fit:cover}
  .lae-colonne__arbre{position:absolute;left:50%;top:0;width:min(128vw,1450px);
    transform:translate3d(-50%,0,0);opacity:.85;will-change:transform;
    filter:drop-shadow(0 0 60px rgba(47,125,79,.35))}
  .lae-colonne__arbre img{width:100%;height:auto}
  /* Assez de voile pour que le texte reste lisible, assez peu pour que l'arbre
     se voie : c'est lui qui porte la descente. */
  .lae-colonne__voile{position:absolute;inset:0;
    background:radial-gradient(130% 80% at 50% 30%,rgba(4,20,12,.1),rgba(4,20,12,.5) 70%,rgba(4,20,12,.72)),
               linear-gradient(180deg,rgba(4,20,12,.42),rgba(4,20,12,.12) 40%,rgba(4,20,12,.5))}
  @media(max-width:960px){.lae-colonne__arbre{width:200vw;opacity:.7}}

  /* Les fonds pleins des scènes deviennent des voiles : sans cela la colonne
     serait masquée précisément là où la descente doit se sentir. */
  .mq{background:rgba(6,26,16,.72)}
  .ds__stick{background:radial-gradient(70% 70% at 50% 40%,rgba(15,36,24,.62),rgba(4,20,12,.9))}
  .arbre__stick{background:radial-gradient(60% 60% at 50% 45%,rgba(15,36,24,.35),rgba(4,20,12,.78))}
  .ds__photo--nu{background:linear-gradient(155deg,rgba(18,48,30,.75),rgba(4,20,12,.9) 68%)}
  .pack,.card,.rz__card{background:rgba(11,29,19,.9);backdrop-filter:blur(2px)}
  .hero__veil{background:
    linear-gradient(180deg,rgba(4,20,12,.72),rgba(4,20,12,.12) 34%,rgba(4,20,12,.42) 68%,rgba(4,20,12,.9)),
    radial-gradient(120% 80% at 22% 60%,transparent 38%,rgba(4,20,12,.55))}
</style>

<script>
/* Masquage de la scène réservé aux visiteurs dont le JS tourne : sans lui,
   la grille des prestations reste lisible au lieu d'attendre une timeline. */
document.documentElement.classList.add('js-cine');
</script>

<div class="lae-cine">

<?php
/* ── LA COLONNE ────────────────────────────────────────────────
   Le décor de toute la page, derrière chaque section : une vidéo qui occupe
   l'écran entier, et un arbre haut de plusieurs écrans que l'on descend en
   défilant — la cime en haut de page, les racines en bas. L'arbre livré est
   un dessin vectoriel du thème ; une photo d'arbre en colonne le remplace dès
   qu'elle est déposée dans le personnalisateur. */
$lae_col_video = lae_reglage( 'cine_video' );
$lae_col_arbre = lae_reglage( 'cine_colonne_image' );
if ( '' === $lae_col_arbre ) {
    $lae_col_arbre = $dir . '/assets/images/arbre-colonne.svg';
}
?>
<div class="lae-colonne" aria-hidden="true">
  <?php if ( $lae_col_video ) : ?>
    <div class="lae-colonne__video">
      <video src="<?php echo esc_url( $lae_col_video ); ?>" muted loop playsinline preload="none" data-lazyplay<?php echo $lae_hero_post ? ' poster="' . esc_url( $lae_hero_post ) . '"' : ''; ?>></video>
    </div>
  <?php elseif ( $lae_hero_post ) : ?>
    <div class="lae-colonne__video"><?php echo lae_img( $lae_hero_post, '', array( 'loading' => '' ) ); ?></div>
  <?php endif; ?>

  <div class="lae-colonne__arbre"><img src="<?php echo esc_url( $lae_col_arbre ); ?>" alt="" decoding="async"></div>
  <div class="lae-colonne__voile"></div>
</div>

<script>
/* La vidéo de fond sort du chemin de chargement critique, et ne démarre
   jamais chez un visiteur qui a demandé moins d'animations. */
(function(){
  if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
  var start = function(){
    document.querySelectorAll('video[data-lazyplay]').forEach(function(v){
      v.preload = 'auto';
      var p = v.play();
      if (p && p.catch) { p.catch(function(){}); }
    });
  };
  if (document.readyState === 'complete') { start(); }
  else { window.addEventListener('load', function(){ setTimeout(start, 200); }); }
})();
</script>

<section class="hero" id="top">
  <div class="hero__veil"></div>

  <div class="hero__in wrap">
    <?php
    /* Premier écran immobile : le métier, la promesse et le moyen d'appeler,
       lisibles sans défiler et sans dépendre d'un script. */
    $lae_h_sur   = lae_reglage( 'hero_surtitre' );
    $lae_h_titre = lae_reglage( 'hero_titre' );
    $lae_h_chapo = lae_reglage( 'hero_chapo' );
    $lae_h_pts   = lae_lignes( 'hero_points' );
    ?>
    <?php if ( $lae_h_sur ) : ?><span class="eyebrow"><?php echo esc_html( $lae_h_sur ); ?></span><?php endif; ?>
    <h1 class="hero__t"><?php echo lae_titre_em( $lae_h_titre ? $lae_h_titre : get_bloginfo( 'name' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h1>
    <?php if ( $lae_h_chapo ) : ?><p class="hero__sub"><?php echo esc_html( $lae_h_chapo ); ?></p><?php endif; ?>

    <div class="hero__cta">
      <?php if ( $lae_contact ) : ?>
        <a class="btn" href="<?php echo esc_url( $lae_contact ); ?>"><span class="ag-l">Demander un devis</span><svg class="ag-trait" viewBox="0 0 34 14" aria-hidden="true"><line x1="1" y1="7" x2="31" y2="7"/><polyline points="25,1 31,7 25,13"/></svg></a>
      <?php endif; ?>
      <?php if ( $lae_tel_lien ) : ?>
        <a class="btn btn--ghost btn--tel" href="<?php echo esc_url( $lae_tel_lien ); ?>">
          <?php echo lae_icone( 'telephone' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
          <?php echo esc_html( $lae_tel ); ?>
        </a>
      <?php endif; ?>
    </div>

    <?php if ( $lae_h_pts ) : ?>
      <ul class="hero__points" style="list-style:none;display:flex;flex-wrap:wrap;gap:10px 24px;margin-top:22px;padding:0;font-size:.86rem;color:var(--muted)">
        <?php foreach ( $lae_h_pts as $lae_pt ) : ?>
          <li style="display:flex;align-items:center;gap:8px"><?php echo lae_icone( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $lae_pt ); ?></span></li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>

  <div class="hero__scroll">DÉFILEZ</div>
</section>

<?php
/* Bandeau : il tourne en continu et accélère avec la vitesse de défilement. */
$lae_mq = lae_lignes( 'cine_marquee' );
if ( $lae_mq ) : ?>
<div class="mq">
  <?php if ( $lae_bois_img ) : ?><div class="mq__bg"><?php echo lae_img( $lae_bois_img, '' ); ?></div><?php endif; ?>
  <div class="mq__in" id="mq">
    <?php foreach ( $lae_mq as $lae_m ) : ?>
      <span><?php echo esc_html( $lae_m ); ?> <b>·</b></span>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>

<?php
/* Tableau épinglé : une grande image qui respire pendant qu'on la traverse. */
$lae_tab_sur   = lae_reglage( 'cine_tab_surtitre' );
$lae_tab_titre = lae_reglage( 'cine_tab_titre' );
$lae_tab_texte = lae_reglage( 'cine_tab_texte' );
if ( $lae_tab_titre || $lae_tab_texte ) : ?>
<section class="tab">
  <div class="tab__stick">
    <div class="tab__img<?php echo $lae_tab_img ? '' : ' tab__img--nu'; ?>" data-tabimg>
      <?php echo lae_img( $lae_tab_img, '' ); ?>
    </div>
    <div class="tab__veil"></div>
    <div class="tab__cap">
      <?php if ( $lae_tab_sur ) : ?><span class="eyebrow" data-rv><?php echo esc_html( $lae_tab_sur ); ?></span><?php endif; ?>
      <?php if ( $lae_tab_titre ) : ?><h2 data-mots><?php echo lae_titre_em( $lae_tab_titre ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2><?php endif; ?>
      <?php if ( $lae_tab_texte ) : ?><p data-txt><?php echo esc_html( $lae_tab_texte ); ?></p><?php endif; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php
/* Chapitres : une ligne par chapitre, « Titre | Texte | mot, mot, mot ».
   L'image de chaque chapitre est un réglage à part (cine_ch1_image…). */
$lae_chs = lae_lignes( 'cine_chapitres' );
if ( $lae_chs ) : ?>
<section class="chs wrap" id="chapitres">
  <?php foreach ( $lae_chs as $lae_i => $lae_ligne ) :
    list( $lae_t, $lae_p, $lae_meta ) = lae_morceaux( $lae_ligne, 3 );
    $lae_ch_img = lae_reglage( 'cine_ch' . ( $lae_i + 1 ) . '_image' );
    ?>
    <article class="ch">
      <div class="ch__txt">
        <div class="ch__n" data-rv><?php echo esc_html( sprintf( '%02d', $lae_i + 1 ) ); ?></div>
        <h3 class="ch__t" data-mots><?php echo lae_titre_em( $lae_t ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h3>
        <?php if ( $lae_p ) : ?><p class="ch__p" data-txt><?php echo esc_html( $lae_p ); ?></p><?php endif; ?>
        <?php if ( $lae_meta ) : ?>
          <div class="ch__meta" data-rv>
            <?php foreach ( array_filter( array_map( 'trim', explode( ',', $lae_meta ) ) ) as $lae_mot ) : ?>
              <span><?php echo esc_html( $lae_mot ); ?></span>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
      <div class="ch__media<?php echo $lae_ch_img ? '' : ' ch__media--nu'; ?>" data-media><?php echo lae_img( $lae_ch_img, '' ); ?></div>
    </article>
  <?php endforeach; ?>
</section>
<?php endif; ?>

<?php
/* ── LA SCÈNE ────────────────────────────────────────────────
   Une seule scène épinglée : l'image se dissout en poussière de feuilles,
   une main paraît, les prestations phares en sortent, s'évaporent, puis la
   grille complète prend leur place. Sur téléphone, le canevas (coûteux)
   laisse la place à une photo qui se dissout en flou. */
$lae_scene_titre = lae_reglage( 'cine_scene_titre' );
$lae_scene_texte = lae_reglage( 'cine_scene_texte' );

$lae_phares = new WP_Query( array(
    'post_type'           => 'lae_prestation',
    'posts_per_page'      => 3,
    'orderby'             => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
    'ignore_sticky_posts' => true,
    'no_found_rows'       => true,
) );

$lae_grille = new WP_Query( array(
    'post_type'           => 'lae_prestation',
    'posts_per_page'      => 8,
    'orderby'             => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
    'ignore_sticky_posts' => true,
    'no_found_rows'       => true,
) );

$lae_familles = get_terms( array( 'taxonomy' => 'lae_famille', 'hide_empty' => true ) );
if ( is_wp_error( $lae_familles ) ) {
    $lae_familles = array();
}
?>
<section class="ds" id="prestations">
  <div class="ds__stick">
    <canvas id="cv"></canvas>
    <?php
    /* La photo et la main sont TOUJOURS présentes, même sans média : la
       timeline les anime nommément, et un élément manquant l'interrompt —
       les prestations ne sortiraient jamais de la paume sur téléphone.
       Sans image, ce sont deux dégradés. */
    echo $lae_scene_img
        ? lae_img( $lae_scene_img, '', array( 'class' => 'ds__photo' ) )
        : '<div class="ds__photo ds__photo--nu" aria-hidden="true"></div>';

    echo $lae_main_img
        ? lae_img( $lae_main_img, '', array( 'class' => 'ds__hand', 'id' => 'hand' ) )
        : '<div class="ds__hand ds__hand--nu" id="hand" aria-hidden="true"></div>';
    ?>
    <div class="ds__veil"></div>

    <?php if ( $lae_scene_titre || $lae_scene_texte ) : ?>
      <div class="ds__cap wrap" id="dsCap">
        <?php if ( $lae_scene_titre ) : ?><h2 data-mots><?php echo lae_titre_em( $lae_scene_titre ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2><?php endif; ?>
        <?php if ( $lae_scene_texte ) : ?><p><?php echo esc_html( $lae_scene_texte ); ?></p><?php endif; ?>
      </div>
    <?php endif; ?>

    <div class="at" id="atStage">
      <div class="wrap">
        <div class="at__head">
          <?php $lae_at_sur = lae_reglage( 'prestations_surtitre', 'Nos prestations' ); ?>
          <?php if ( $lae_at_sur ) : ?><span class="eyebrow"><?php echo esc_html( $lae_at_sur ); ?></span><?php endif; ?>
          <h2 class="stitle" data-mots style="margin:12px 0 14px"><?php echo lae_titre_em( lae_reglage( 'prestations_titre', 'Élagage, abattage et *création de jardin*' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
          <?php $lae_at_chapo = lae_reglage( 'prestations_chapo' ); ?>
          <?php if ( $lae_at_chapo ) : ?><p class="lead" style="margin:0 auto"><?php echo esc_html( $lae_at_chapo ); ?></p><?php endif; ?>
        </div>

        <?php if ( $lae_familles ) : ?>
          <div class="at__filters">
            <button class="fbtn is-on" data-f="tous">Tous</button>
            <?php foreach ( $lae_familles as $lae_f ) : ?>
              <button class="fbtn" data-f="f-<?php echo (int) $lae_f->term_id; ?>"><?php echo esc_html( $lae_f->name ); ?></button>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <div class="at__grid" id="grid">
          <?php
          while ( $lae_grille->have_posts() ) :
              $lae_grille->the_post();
              $lae_termes = get_the_terms( get_the_ID(), 'lae_famille' );
              $lae_cat    = ( $lae_termes && ! is_wp_error( $lae_termes ) ) ? 'f-' . (int) $lae_termes[0]->term_id : '';
              $lae_icone  = get_post_meta( get_the_ID(), '_lae_icone', true );
              ?>
              <a class="card" data-cat="<?php echo esc_attr( $lae_cat ); ?>" href="<?php the_permalink(); ?>">
                <div class="card__media<?php echo has_post_thumbnail() ? '' : ' card__media--nu'; ?>">
                  <?php if ( $lae_termes && ! is_wp_error( $lae_termes ) ) : ?>
                    <span class="card__badge"><?php echo esc_html( $lae_termes[0]->name ); ?></span>
                  <?php endif; ?>
                  <?php
                  if ( has_post_thumbnail() ) {
                      the_post_thumbnail( 'lae-carte', array( 'loading' => 'lazy', 'alt' => esc_attr( get_the_title() ) ) );
                  } elseif ( $lae_icone ) {
                      echo '<span class="card__glyphe">' . lae_icone( $lae_icone ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput
                  }
                  ?>
                </div>
                <div class="card__body">
                  <div class="card__t"><?php the_title(); ?></div>
                  <?php if ( has_excerpt() ) : ?><div class="card__d"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 14 ) ); ?></div><?php endif; ?>
                  <div class="card__go">Voir →</div>
                </div>
              </a>
          <?php endwhile; wp_reset_postdata(); ?>
        </div>
      </div>
    </div>

    <div class="of" id="ofStage" data-ancre="phares">
      <?php if ( $lae_bois_img ) : ?><div class="of__bg"><?php echo lae_img( $lae_bois_img, '' ); ?></div><?php endif; ?>
      <div class="wrap of__in">
        <?php $lae_of_sur = lae_reglage( 'cine_phares_surtitre' ); ?>
        <?php if ( $lae_of_sur ) : ?><span class="eyebrow" data-rv><?php echo esc_html( $lae_of_sur ); ?></span><?php endif; ?>
        <h2 class="stitle" data-mots style="margin-top:10px"><?php echo lae_titre_em( lae_reglage( 'cine_phares_titre', 'Trois métiers, *un seul interlocuteur*' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
        <?php $lae_of_chapo = lae_reglage( 'cine_phares_chapo' ); ?>
        <?php if ( $lae_of_chapo ) : ?><p class="lead" data-rv style="margin:16px auto 0"><?php echo esc_html( $lae_of_chapo ); ?></p><?php endif; ?>

        <div class="of__grid">
          <?php
          while ( $lae_phares->have_posts() ) :
              $lae_phares->the_post();
              $lae_tms = get_the_terms( get_the_ID(), 'lae_famille' );
              $lae_ico = get_post_meta( get_the_ID(), '_lae_icone', true );
              $lae_nu  = ! has_post_thumbnail();
              ?>
              <article class="pack<?php echo $lae_nu ? ' pack--nu' : ''; ?>" data-pack>
                <?php if ( ! $lae_nu ) : ?>
                  <?php the_post_thumbnail( 'lae-carte', array( 'loading' => 'lazy', 'alt' => esc_attr( get_the_title() ) ) ); ?>
                <?php endif; ?>
                <div class="pack__body">
                  <div class="pack__txt">
                    <h3 class="pack__nom">
                      <?php if ( $lae_nu && $lae_ico ) : ?><span class="pack__ic"><?php echo lae_icone( $lae_ico ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span><?php endif; ?>
                      <?php the_title(); ?>
                    </h3>
                    <?php if ( has_excerpt() ) : ?>
                      <p class="pack__res"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 24 ) ); ?></p>
                    <?php endif; ?>
                    <?php if ( $lae_tms && ! is_wp_error( $lae_tms ) ) : ?>
                      <p class="pack__delai"><?php echo esc_html( $lae_tms[0]->name ); ?></p>
                    <?php endif; ?>
                  </div>
                  <a class="btn pack__cta" href="<?php the_permalink(); ?>">Voir <?php the_title(); ?></a>
                </div>
              </article>
          <?php endwhile; wp_reset_postdata(); ?>
        </div>

        <?php $lae_of_note = lae_reglage( 'cine_phares_note' ); ?>
        <?php if ( $lae_of_note ) : ?>
          <p class="of__note" data-rv><?php echo esc_html( $lae_of_note ); ?><?php if ( $lae_contact ) : ?> — <a href="<?php echo esc_url( $lae_contact ); ?>">demander un devis</a><?php endif; ?>.</p>
        <?php endif; ?>
      </div>
    </div>

  </div>
</section>

<?php
/* Réalisations : les chantiers saisis dans l'administration. */
$lae_rz = new WP_Query( array(
    'post_type'           => 'lae_realisation',
    'posts_per_page'      => 3,
    'ignore_sticky_posts' => true,
    'no_found_rows'       => true,
) );

/* Avis : uniquement de VRAIS avis, recopiés depuis Google par le client.
   Aucun repli, aucun témoignage écrit par nous : une section absente vaut
   mieux qu'une section fausse. */
$lae_avis     = lae_lignes( 'cine_avis' );
$lae_avis_url = lae_reglage( 'cine_avis_url' );
$lae_avis_note = lae_reglage( 'cine_avis_note' );
$lae_avis_tot  = lae_reglage( 'cine_avis_total' );

if ( $lae_rz->have_posts() || $lae_avis ) : ?>
<section class="rz" id="realisations">
  <div class="rz__glow"></div>
  <div class="wrap rz__in">
    <span class="eyebrow" data-rv>Réalisations</span>
    <h2 class="stitle" data-mots style="margin:12px 0 16px"><?php echo lae_titre_em( lae_reglage( 'realisations_titre', 'Ce qu\'on livre, *pour de vrai*' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
    <?php $lae_rz_chapo = lae_reglage( 'realisations_chapo' ); ?>
    <?php if ( $lae_rz_chapo ) : ?><p class="lead" data-txt style="margin:0 auto"><?php echo esc_html( $lae_rz_chapo ); ?></p><?php endif; ?>

    <?php if ( $lae_rz->have_posts() ) : ?>
      <div class="rz__grid">
        <?php
        while ( $lae_rz->have_posts() ) :
            $lae_rz->the_post();
            $lae_type = get_the_terms( get_the_ID(), 'lae_type_chantier' );
            ?>
            <article class="rz__card" data-rv>
              <a class="rz__vue<?php echo has_post_thumbnail() ? '' : ' rz__vue--nu'; ?>" href="<?php the_permalink(); ?>">
                <?php if ( has_post_thumbnail() ) { the_post_thumbnail( 'lae-carte', array( 'loading' => 'lazy', 'alt' => esc_attr( get_the_title() ) ) ); } ?>
              </a>
              <div class="rz__body">
                <?php if ( $lae_type && ! is_wp_error( $lae_type ) ) : ?>
                  <span class="rz__meta"><?php echo esc_html( $lae_type[0]->name ); ?></span>
                <?php endif; ?>
                <h3><?php the_title(); ?></h3>
                <?php if ( has_excerpt() ) : ?><p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 28 ) ); ?></p><?php endif; ?>
                <div class="rz__liens"><a href="<?php the_permalink(); ?>">Voir le chantier →</a></div>
              </div>
            </article>
        <?php endwhile; wp_reset_postdata(); ?>
      </div>
    <?php endif; ?>

    <?php if ( $lae_avis ) : ?>
      <div class="av" data-rv>
        <div class="av__head">
          <span class="av__g" aria-hidden="true"><svg viewBox="0 0 24 24" width="22" height="22"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.27-4.74 3.27-8.1z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.99.66-2.26 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84A11 11 0 0 0 12 23z"/><path fill="#FBBC05" d="M5.84 14.1a6.6 6.6 0 0 1 0-4.2V7.06H2.18a11 11 0 0 0 0 9.88l3.66-2.84z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84C6.71 7.31 9.14 5.38 12 5.38z"/></svg></span>
          <span class="av__rate">
            <?php if ( $lae_avis_note ) : ?><strong><?php echo esc_html( $lae_avis_note ); ?></strong><?php endif; ?>
            <?php echo $lae_avis_tot ? esc_html( $lae_avis_tot . ' avis Google' ) : 'Avis Google'; ?>
          </span>
        </div>
        <div class="av__grid">
          <?php foreach ( array_slice( $lae_avis, 0, 3 ) as $lae_a ) :
            list( $lae_nom, $lae_txt, $lae_quand ) = lae_morceaux( $lae_a, 3 );
            if ( '' === $lae_txt ) { continue; }
            ?>
            <article class="av__card">
              <div class="av__top">
                <span class="av__ava"><?php echo esc_html( mb_strtoupper( mb_substr( $lae_nom ? $lae_nom : 'C', 0, 1 ) ) ); ?></span>
                <span class="av__who">
                  <span class="av__nom"><?php echo esc_html( $lae_nom ? $lae_nom : 'Client Google' ); ?></span>
                  <?php if ( $lae_quand ) : ?><span class="av__role"><?php echo esc_html( $lae_quand ); ?></span><?php endif; ?>
                </span>
              </div>
              <p class="av__q">«&nbsp;<?php echo esc_html( $lae_txt ); ?>&nbsp;»</p>
              <?php if ( $lae_avis_url ) : ?><a class="av__lien" href="<?php echo esc_url( $lae_avis_url ); ?>" target="_blank" rel="noopener">Voir sur Google →</a><?php endif; ?>
            </article>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>

    <?php $lae_rz_lien = get_post_type_archive_link( 'lae_realisation' ); ?>
    <?php if ( $lae_rz_lien ) : ?>
      <a class="btn" data-rv href="<?php echo esc_url( $lae_rz_lien ); ?>">Tous les chantiers</a>
    <?php endif; ?>
  </div>
</section>
<?php endif; ?>

<?php
/* Révélation : l'arbre paraît, et l'appel se pose dessus. */
$lae_ar_sur   = lae_reglage( 'cine_arbre_surtitre' );
$lae_ar_titre = lae_reglage( 'cine_arbre_titre' );
$lae_ar_texte = lae_reglage( 'cine_arbre_texte' );
if ( $lae_ar_titre || $lae_arbre_img ) : ?>
<section class="arbre" id="devis">
  <div class="arbre__stick">
    <?php
    echo $lae_arbre_img
        ? lae_img( $lae_arbre_img, '', array( 'class' => 'arbre__img', 'id' => 'arbreImg' ) )
        : '<div class="arbre__img arbre__img--nu" id="arbreImg"></div>';
    ?>
    <div class="arbre__veil"></div>

    <div class="arbre__panel" id="arbrePanel">
      <?php if ( $lae_ar_sur ) : ?><span class="eyebrow"><?php echo esc_html( $lae_ar_sur ); ?></span><?php endif; ?>
      <?php if ( $lae_ar_titre ) : ?><h2 class="stitle"><?php echo lae_titre_em( $lae_ar_titre ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2><?php endif; ?>
      <?php if ( $lae_ar_texte ) : ?><p class="arbre__lead"><?php echo esc_html( $lae_ar_texte ); ?></p><?php endif; ?>

      <div class="hero__cta" style="justify-content:center;margin-top:26px">
        <?php if ( $lae_tel_lien ) : ?>
          <a class="btn btn--tel" href="<?php echo esc_url( $lae_tel_lien ); ?>">
            <?php echo lae_icone( 'telephone' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
            <?php echo esc_html( $lae_tel ); ?>
          </a>
        <?php endif; ?>
        <?php if ( $lae_contact ) : ?>
          <a class="btn btn--ghost" href="<?php echo esc_url( $lae_contact ); ?>">Demander un devis</a>
        <?php endif; ?>
      </div>
    </div>

    <div class="arbre__w"><?php bloginfo( 'name' ); ?></div>
  </div>
</section>
<?php endif; ?>

<?php
/* Appel final. */
$lae_cta_titre = lae_reglage( 'appel_titre', 'Un projet, un arbre à traiter ?' );
$lae_cta_texte = lae_reglage( 'appel_texte' );
if ( $lae_tel_lien || $lae_contact ) : ?>
<section class="cta wrap">
  <h2 class="stitle" data-mots><?php echo lae_titre_em( $lae_cta_titre ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
  <?php if ( $lae_cta_texte ) : ?><p data-rv><?php echo esc_html( $lae_cta_texte ); ?></p><?php endif; ?>
  <?php if ( $lae_contact ) : ?>
    <a class="btn" data-rv href="<?php echo esc_url( $lae_contact ); ?>"><?php echo esc_html( lae_reglage( 'appel_btn_texte', 'Demander un devis' ) ); ?></a>
  <?php elseif ( $lae_tel_lien ) : ?>
    <a class="btn" data-rv href="<?php echo esc_url( $lae_tel_lien ); ?>"><?php echo esc_html( $lae_tel ); ?></a>
  <?php endif; ?>
</section>
<?php endif; ?>

</div><!-- /.lae-cine -->

<script src="<?php echo esc_url( $dir . '/assets/js/lib/gsap.min.js' ); ?>"></script>
<script src="<?php echo esc_url( $dir . '/assets/js/lib/ScrollTrigger.min.js' ); ?>"></script>
<script src="<?php echo esc_url( $dir . '/assets/js/lib/lenis.min.js' ); ?>"></script>

<script>
var LAE_SCENE_IMG = <?php echo wp_json_encode( $lae_scene_img ); ?>;

(function(){
  var G = window.gsap, ST = window.ScrollTrigger;

  /* RÉDUCTION DE MOUVEMENT — coupure nette (DESIGN.md).
     La règle CSS @media(prefers-reduced-motion) ne coupait que les animations
     CSS ; toutes celles de cette page sont pilotées par GSAP en styles inline.
     Résultat mesuré le 03/09 : sous « réduire les animations », les cartes de
     prix restaient à opacity:0 sur TOUTE la hauteur de la page — le prix
     n'existait tout simplement pas pour ces visiteurs.
     Même chose si GSAP ne se charge pas (4G coupée, script bloqué) : .at reste
     à opacity:0 (CSS) et .of à pointer-events:none, donc rien n'est cliquable.
     On force donc l'état FINAL : tout visible, tout cliquable, sans timeline. */
  var REDUIT = !!(window.matchMedia && matchMedia("(prefers-reduced-motion: reduce)").matches);
  var ok = !!(G && ST) && !REDUIT;
  if (ok) G.registerPlugin(ST);

  if (!ok) {
    (function etatFinal(){
      var etape = document.querySelector(".ds");
      var colle = document.querySelector(".ds__stick");
      if (etape) { etape.style.height = "auto"; }
      if (colle) { colle.style.position = "static"; colle.style.height = "auto"; }
      document.querySelectorAll(".of, .at, .of__grid, .pack, [data-rv], [data-mots], [data-hl]").forEach(function(el){
        el.style.opacity = "1";
        el.style.transform = "none";
        el.style.filter = "none";
        el.style.pointerEvents = "auto";
      });
      document.querySelectorAll(".of, .at").forEach(function(el){
        el.style.position = "static";
        el.style.visibility = "visible";
      });
      document.querySelectorAll(".pack").forEach(function(el){ el.style.position = "static"; });
      var cap = document.getElementById("dsCap");
      if (cap) { cap.style.display = "none"; }
    })();
  }

  /* défilement amorti — jamais sous réduction de mouvement */
  var L = REDUIT ? null : (window.Lenis || (window.lenis && window.lenis.default));
  if (L) {
    var lenis = new L({ duration: 1.15, smoothWheel: true });
    (function raf(t){ lenis.raf(t); requestAnimationFrame(raf); })();
    if (ok) lenis.on("scroll", ST.update);
  }

  /* en-tête compact */
  var hd = document.querySelector(".lae-header");
  if (hd) addEventListener("scroll", function(){ hd.classList.toggle("is-stuck", scrollY > 60); }, { passive:true });

  if (ok) {
    /* titre mot à mot */
    var t = document.querySelector("[data-split]");
    if (t) {
      t.innerHTML = t.innerHTML.split(" ").map(function(w){ return '<span class="w"><i>'+w+'</i></span>'; }).join(" ");
      G.from(t.querySelectorAll(".w i"), { yPercent:118, duration:1.15, ease:"expo.out", stagger:.09, delay:.15 });
    }
    G.from("[data-hl]", { y:26, opacity:0, duration:1, ease:"power3.out", stagger:.12, delay:.5 });
    G.from("[data-eg]", { y:44, opacity:0, duration:1.3, ease:"power3.out", delay:.25 });

    /* hero : parallaxe + on se rapproche de la main */
    G.to("[data-parallax] img", { yPercent:12, ease:"none", scrollTrigger:{ trigger:".hero", start:"top top", end:"bottom top", scrub:true }});
    if (!matchMedia("(max-width:960px)").matches)
      G.to("[data-eg]", { scale:1.6, ease:"none", scrollTrigger:{ trigger:".hero", start:"top top", end:"bottom top", scrub:.5 }});
    G.to(".hero__in", { y:-40, opacity:0, ease:"none", scrollTrigger:{ trigger:".hero", start:"top top", end:"55% top", scrub:true }});
    G.to(".feuillemark", { scale:1.25, opacity:.02, ease:"none", scrollTrigger:{ trigger:".hero", start:"top top", end:"bottom top", scrub:true }});

    /* bandeau : boucle continue en rAF, accélérée par la vitesse de scroll */
    (function(){
      var mq = document.getElementById("mq");
      mq.innerHTML += mq.innerHTML;            // deux copies pour boucler sans trou
      var x = 0, boost = 1, half = 0, last = 0;
      function measure(){ half = mq.scrollWidth / 2; }
      measure(); addEventListener("resize", measure);
      ST.create({ onUpdate: function(self){
        boost = 1 + Math.min(5, Math.abs(self.getVelocity()) / 900);
      }});
      G.ticker.add(function(t){
        var dt = last ? Math.min(0.05, t - last) : 0.016; last = t;
        if (!half) measure();
        x -= 46 * dt * boost;                  // 46 px/s au repos
        if (half && x <= -half) x += half;     // rebouclage exact
        mq.style.transform = "translate3d(" + x + "px,0,0)";
        boost += (1 - boost) * 0.06;           // retour au calme
      });
    })();

    /* tableau : Ken Burns lent */
    if (!matchMedia("(max-width:960px)").matches)
      G.fromTo("[data-tabimg] img", { scale:1.02, yPercent:-3 }, { scale:1.16, yPercent:3, ease:"none",
        scrollTrigger:{ trigger:".tab", start:"top top", end:"bottom bottom", scrub:true }});

    /* titres : chaque mot monte derrière un masque */
    G.utils.toArray("[data-mots]").forEach(function(h){
      var html = h.innerHTML;
      h.innerHTML = html.replace(/(<em>|<\/em>)/g, "\u0001$1\u0001").split("\u0001").map(function(part){
        if (part.charAt(0) === "<") return part;
        return part.split(" ").map(function(w){ return w ? '<span class="w"><i>'+w+'</i></span>' : ""; }).join(" ");
      }).join("");
      G.from(h.querySelectorAll(".w i"), { yPercent:118, duration:1.05, ease:"expo.out", stagger:.055,
        scrollTrigger:{ trigger:h, start:"top 88%" }});
    });
    /* paragraphes : montée + léger flou qui se lève, puis dérive douce */
    G.utils.toArray("[data-txt]").forEach(function(p){
      G.from(p, { y:26, opacity:0, filter:"blur(6px)", duration:1, ease:"power3.out",
        scrollTrigger:{ trigger:p, start:"top 90%" }});
      G.to(p, { y:-22, ease:"none", scrollTrigger:{ trigger:p, start:"top bottom", end:"bottom top", scrub:true }});
    });

    /* révélations */
    G.utils.toArray("[data-rv]").forEach(function(el){
      G.from(el, { y:32, opacity:0, duration:.95, ease:"power3.out", scrollTrigger:{ trigger:el, start:"top 88%" }});
    });
    G.utils.toArray("[data-media]").forEach(function(m, i){
      var sens = i % 2 ? -1 : 1;
      /* l'image vit pendant tout son passage : zoom, glissement et redressement */
      G.fromTo(m.querySelector("img"), { scale:1.32, yPercent:-10 }, { scale:1.02, yPercent:10, ease:"none",
        scrollTrigger:{ trigger:m, start:"top bottom", end:"bottom top", scrub:.4 }});
      G.fromTo(m, { yPercent:9 * sens, rotate:1.4 * sens }, { yPercent:-9 * sens, rotate:0, ease:"none",
        scrollTrigger:{ trigger:m, start:"top bottom", end:"bottom top", scrub:.4 }});
      G.from(m, { clipPath:"inset(100% 0% 0% 0%)", duration:1.2, ease:"power4.out", scrollTrigger:{ trigger:m, start:"top 86%" }});
      /* La colonne de texte arrive par le côté opposé sur PC.
         Sur téléphone, cette entrée latérale de 52 px poussait la colonne
         hors du cadre : la page entière défilait horizontalement de 37 px
         (mesuré à 375 comme à 390). On la remplace par l'entrée documentée
         dans DESIGN.md — fondu + léger déplacement VERTICAL — qui ne peut
         pas déborder. */
      var txt = m.parentNode.querySelector(".ch__txt");
      var etroit = matchMedia("(max-width:960px)").matches;
      if (txt) G.fromTo(txt,
        etroit ? { y:18, opacity:.35 } : { x:52 * -sens, opacity:.35 },
        { x:0, y:0, opacity:1, ease:"power2.out",
          scrollTrigger:{ trigger:m.parentNode, start:"top 92%", end:"top 45%", scrub:.5 }});
      /* le numéro file plus vite */
      var n = m.parentNode.querySelector(".ch__n");
      if (n) G.to(n, { yPercent:-70, ease:"none", scrollTrigger:{ trigger:m.parentNode, start:"top bottom", end:"bottom top", scrub:true }});
    });
    /* arbre : la revelation se joue sur la premiere moitie du defilement,
       pour que le formulaire pose par-dessus soit utilisable tout de suite. */
    G.fromTo("#arbreImg", { scale:.5, opacity:0, filter:"blur(14px)" },
      { scale:1, opacity:1, filter:"blur(0px)", ease:"none",
        scrollTrigger:{ trigger:".arbre", start:"top top", end:"center bottom", scrub:.6 }});
    G.from("#arbrePanel", { y:26, opacity:0, duration:.9, ease:"power3.out",
      scrollTrigger:{ trigger:".arbre", start:"top 62%" }});
  }

  /* filtres de l'atelier */
  var cards = Array.prototype.slice.call(document.querySelectorAll("#grid .card"));
  document.querySelectorAll(".fbtn").forEach(function(b){
    b.addEventListener("click", function(){
      document.querySelectorAll(".fbtn").forEach(function(x){ x.classList.remove("is-on"); });
      b.classList.add("is-on");
      var f = b.dataset.f;
      cards.forEach(function(c){
        var show = (f === "tous" || c.dataset.cat === f);
        if (ok) {
          G.to(c, { opacity: show ? 1 : 0, scale: show ? 1 : .96, duration:.35, ease:"power2.out",
            onStart: function(){ if (show) c.style.display = ""; },
            onComplete: function(){ if (!show) c.style.display = "none"; if (ST) ST.refresh(); }});
        } else { c.style.display = show ? "" : "none"; }
      });
    });
  });

  /* ---- dissolution en pixels dorés ---- */
  var cv = document.getElementById("cv"), ctx = cv.getContext("2d");
  var img = new Image(); img.src = LAE_SCENE_IMG;
  var cell = 12, cols = 0, rows = 0, data = null, seed = null, progress = 0, ready = false;

  function build(){
    var dpr = Math.min(devicePixelRatio || 1, 2);
    cv.width = Math.floor(cv.clientWidth * dpr); cv.height = Math.floor(cv.clientHeight * dpr);
    if (!img.complete || !img.naturalWidth) return;
    cols = Math.ceil(cv.width / cell); rows = Math.ceil(cv.height / cell);
    var c = document.createElement("canvas"); c.width = cols; c.height = rows;
    var cx = c.getContext("2d");
    var r = Math.max(cols / img.naturalWidth, rows / img.naturalHeight);
    var w = img.naturalWidth * r, h = img.naturalHeight * r;
    cx.drawImage(img, (cols - w) / 2, (rows - h) / 2 * 1.1, w, h);
    data = cx.getImageData(0, 0, cols, rows).data;
    seed = new Float32Array(cols * rows);
    for (var i = 0; i < seed.length; i++) seed[i] = Math.random();
    ready = true; draw();
  }

  function draw(){
    if (!ready) return;
    ctx.fillStyle = "#04140c"; ctx.fillRect(0, 0, cv.width, cv.height);
    var r = Math.max(cv.width / img.naturalWidth, cv.height / img.naturalHeight);
    var iw = img.naturalWidth * r, ih = img.naturalHeight * r;
    ctx.drawImage(img, (cv.width - iw) / 2, (cv.height - ih) / 2 * 1.1, iw, ih);
    var p = progress;
    for (var y = 0; y < rows; y++) for (var x = 0; x < cols; x++){
      var i = y * cols + x;
      var th = (x / cols) * .45 + (y / rows) * .25 + seed[i] * .55;
      var k = (p - th) / .35;
      if (k <= 0) continue;
      ctx.fillStyle = "#04140c"; ctx.fillRect(x * cell, y * cell, cell + 1, cell + 1);
      if (k < 1){
        var a = 1 - k, dy = k * cell * 5 * (seed[i] - .3), dx = k * cell * 3 * (seed[i] - .5);
        ctx.fillStyle = "rgba(" + Math.round(212 + 32 * k) + "," + Math.round(180 + 28 * k) + "," + Math.round(92 + 20 * k) + "," + (a * .95) + ")";
        var s = cell * (1 - k * .6);
        ctx.fillRect(x * cell + dx, y * cell - dy, s, s);
      }
    }
  }

  var petitEcran = matchMedia("(max-width:960px)").matches;
  if (!petitEcran){ img.onload = build; addEventListener("resize", build); }
  if (ok && !petitEcran){
    ST.create({ trigger:".ds", start:"top top", end:"bottom bottom", scrub:true,
      onUpdate: function(self){ progress = Math.min(1.15, self.progress / .30 * 1.12); draw(); }});
  }
  if (ok){
    /* SCÈNE UNIQUE : dissolution -> main -> offres -> évaporation -> atelier */
    (function(){
      /* ---------------------------------------------------------------
         TABLETTE ET MOBILE : même scène, même main, mais les offres
         sortent de la paume UNE PAR UNE pour tenir dans le cadre, et la
         dissolution en pixels (canvas) laisse la place a un flou.
         --------------------------------------------------------------- */
      if (matchMedia("(max-width:960px)").matches){
        var stM   = document.querySelector(".ds__stick");
        var handM = document.getElementById("hand");
        var photo = document.querySelector(".ds__photo");
        var ofM   = document.getElementById("ofStage");
        var atM   = document.getElementById("atStage");
        var packM = G.utils.toArray("#ofStage [data-pack]");
        var cardM = G.utils.toArray("#atStage .card");

        /* la paume : mesurée sur la main réelle, insensible aux transforms */
        function paumeM(){ return { x: handM.offsetLeft + handM.offsetWidth / 2,
                                    y: handM.offsetTop  + handM.offsetHeight * .52 }; }
        function centreM(el){ var x=0,y=0,n=el; while(n && n!==stM){ x+=n.offsetLeft; y+=n.offsetTop; n=n.offsetParent; }
                              return { x:x+el.offsetWidth/2, y:y+el.offsetHeight/2 }; }

        G.set([ofM, atM], { opacity:0 });
        G.set(".at__filters", { opacity:0, y:12 });
        G.set(packM, { opacity:0 });

        var tm = G.timeline({ scrollTrigger:{ trigger:".ds", start:"top top", end:"bottom bottom",
          scrub:.5, invalidateOnRefresh:true,
          onUpdate:function(self){
            var p = self.progress;
            ofM.classList.toggle("is-live", p > .30 && p < .56);
            atM.classList.toggle("is-live", p > .64);
          }}});

        /* la photo se dissout, la main paraît */
        tm.to(photo, { opacity:0, scale:1.2, filter:"blur(20px)", duration:.16, ease:"power2.in" }, .04)
          .fromTo(handM, { opacity:0, scale:.8, yPercent:8 },
                         { opacity:.98, scale:1, yPercent:0, duration:.06, ease:"power2.out" }, .17)
          .to("#dsCap", { opacity:0, y:-26, duration:.05, ease:"power2.in" }, .20)
          .to(ofM, { opacity:1, duration:.02 }, .26)
          .fromTo("#ofStage .of__in > *:not(.of__grid)", { opacity:0, y:26 },
                  { opacity:1, y:0, duration:.05, stagger:.02, ease:"power3.out" }, .27)
          .to(handM, { opacity:.34, scale:1.16, duration:.10, ease:"none" }, .30);

        /* Les offres jaillissent de la paume, TOUTES ENSEMBLE et elles restent.
           Avant, sur téléphone (≤700 px), chaque carte était détruite ~0,32
           écran avant l'arrivée de la suivante : les trois prix n'existaient
           jamais simultanément, donc 490 / 890 / 1490 n'étaient pas
           comparables — alors que c'est la décision même du visiteur.
           Elles tiennent maintenant ensemble parce que le pack est affiché en
           texte compact (image masquée sous 960 px). */
        var T0 = .30, PAS = .022;
        packM.forEach(function(c, i){
          tm.fromTo(c,
            { opacity:0, scale:.08, rotation:(i - 1) * 26,
              x:function(){ return paumeM().x - centreM(c).x; },
              y:function(){ return paumeM().y - centreM(c).y; } },
            { keyframes:[
                { opacity:1, scale:.46, rotation:(i - 1) * 12, duration:.5,
                  x:function(){ return (paumeM().x - centreM(c).x) * .4; },
                  y:function(){ return (paumeM().y - centreM(c).y) * .5 - 30; } },
                { opacity:1, scale:1, rotation:0, x:0, y:0, duration:.5, ease:"power3.out" }
              ], ease:"none", duration:.06 },
            T0 + i * PAS);
          /* Sortie groupée seulement quand la scène passe à l'atelier : plus
             aucune carte n'est détruite tant que les offres sont à l'écran. */
          tm.to(c, { opacity:0, scale:.82, y:-60, filter:"blur(7px)", duration:.032, ease:"power2.in" },
            .55 + i * .012);
        });

        /* les offres s'effacent, l'atelier prend la place */
        tm.to("#ofStage .of__in > *:not(.of__grid)", { opacity:0, y:-34, duration:.05, ease:"power2.in" }, .55)
          .to(ofM, { opacity:0, duration:.02 }, .61)
          .to(handM, { opacity:.95, scale:1, duration:.06, ease:"power2.out" }, .55)
          .to(atM, { opacity:1, duration:.02 }, .61)
          .fromTo(".at__head", { opacity:0, y:26 }, { opacity:1, y:0, duration:.06, ease:"power3.out" }, .62)
          .to(handM, { opacity:.18, scale:1.24, duration:.12, ease:"none" }, .66);

        cardM.forEach(function(c, i){
          var a0 = i * (Math.PI * 2 / cardM.length);
          tm.fromTo(c,
            { opacity:0, scale:.07, rotation:(i % 2 ? 1 : -1) * (240 + i * 30),
              x:function(){ return paumeM().x - centreM(c).x + Math.cos(a0) * 18; },
              y:function(){ return paumeM().y - centreM(c).y + Math.sin(a0) * 18; } },
            { keyframes:[
                { opacity:1, scale:.4, rotation:(i % 2 ? 1 : -1) * 100, duration:.5,
                  x:function(){ return (paumeM().x - centreM(c).x) * .42 + Math.cos(a0 + 2.2) * 70; },
                  y:function(){ return (paumeM().y - centreM(c).y) * .42 + Math.sin(a0 + 2.2) * 60; } },
                { opacity:1, scale:1, rotation:0, x:0, y:0, duration:.5, ease:"power3.out" }
              ], ease:"none", duration:.13 },
            .66 + i * .014);
        });

        tm.to(".at__filters", { opacity:1, y:0, duration:.05, ease:"power2.out" }, .88);
        return;
      }
      var stick = document.querySelector(".ds__stick");
      var of = document.getElementById("ofStage"), at = document.getElementById("atStage");
      var packs = G.utils.toArray("#ofStage [data-pack]");
      var cards = G.utils.toArray("#atStage .card");
      function paume(){ return { x: stick.clientWidth / 2, y: stick.clientHeight * 0.30 }; }
      function centre(el){ var x=0,y=0,n=el; while(n && n!==stick){ x+=n.offsetLeft; y+=n.offsetTop; n=n.offsetParent; }
        return { x:x+el.offsetWidth/2, y:y+el.offsetHeight/2 }; }

      G.set([of, at], { opacity:0 });
      G.set(".at__filters", { opacity:0, y:14 });

      var tl = G.timeline({ scrollTrigger:{ trigger:".ds", start:"top top", end:"bottom bottom",
        scrub:.5, invalidateOnRefresh:true,
        onUpdate:function(self){
          var p = self.progress;
          of.classList.toggle("is-live", p > .42 && p < .62);
          at.classList.toggle("is-live", p > .80);
        }}});

      /* la main ne paraît qu'une fois l'image entièrement dissoute */
      tl.fromTo("#hand", { opacity:0, scale:.78, yPercent:10 },
                         { opacity:.95, scale:1.05, yPercent:0, duration:.06, ease:"power2.out" }, .30)
        .to("#dsCap", { opacity:0, y:-30, duration:.04, ease:"power2.in" }, .30)
        /* les offres sortent de la paume */
        .to(of, { opacity:1, duration:.02 }, .36)
        .fromTo("#ofStage .of__in > *:not(.of__grid)", { opacity:0, y:34 },
                { opacity:1, y:0, duration:.06, stagger:.02, ease:"power3.out" }, .37)
        .to("#hand", { opacity:.2, scale:1.2, duration:.12, ease:"none" }, .40);

      packs.forEach(function(c, i){
        tl.fromTo(c,
          { opacity:0, scale:.07, rotation:(i - 1) * 26,
            x:function(){ return paume().x - centre(c).x; },
            y:function(){ return paume().y - centre(c).y; } },
          { keyframes:[
              { opacity:1, scale:.5, rotation:(i - 1) * 14, duration:.5,
                x:function(){ return (paume().x - centre(c).x) * .45 + (i - 1) * 150; },
                y:function(){ return (paume().y - centre(c).y) * .5 - 60; } },
              { opacity:1, scale:1, rotation:0, x:0, y:0, duration:.5, ease:"power3.out" }
            ], ease:"none", duration:.13 },
          .38 + i * .025);
      });

      /* les offres s'évaporent, la main se rallume */
      tl.to(packs, { opacity:0, scale:.86, y:-70, filter:"blur(8px)", duration:.09, stagger:.02, ease:"power2.in" }, .62)
        .to("#ofStage .of__in > *:not(.of__grid)", { opacity:0, y:-40, duration:.06, ease:"power2.in" }, .63)
        .to(of, { opacity:0, duration:.02 }, .72)
        .to("#hand", { opacity:.95, scale:1.05, duration:.06, ease:"power2.out" }, .64)
        /* l'atelier prend sa place */
        .to(at, { opacity:1, duration:.02 }, .70)
        .fromTo(".at__head", { opacity:0, y:30 }, { opacity:1, y:0, duration:.06, ease:"power3.out" }, .71)
        .to("#hand", { opacity:.16, scale:1.28, duration:.14, ease:"none" }, .74);

      cards.forEach(function(c, i){
        var a0 = i * (Math.PI * 2 / cards.length);
        tl.fromTo(c,
          { opacity:0, scale:.06, rotation:(i % 2 ? 1 : -1) * (300 + i * 40),
            x:function(){ return paume().x - centre(c).x + Math.cos(a0) * 26; },
            y:function(){ return paume().y - centre(c).y + Math.sin(a0) * 26; } },
          { keyframes:[
              { opacity:1, scale:.42, rotation:(i % 2 ? 1 : -1) * 120, duration:.5,
                x:function(){ return (paume().x - centre(c).x) * .45 + Math.cos(a0 + 2.2) * 200; },
                y:function(){ return (paume().y - centre(c).y) * .45 + Math.sin(a0 + 2.2) * 130; } },
              { opacity:1, scale:1, rotation:0, x:0, y:0, duration:.5, ease:"power3.out" }
            ], ease:"none", duration:.16 },
          .74 + i * .016);
      });

      tl.to(".at__filters", { opacity:1, y:0, duration:.05, ease:"power2.out" }, .93);
    })();
  }

  /* ── Ancres vers la scène épinglée ───────────────────────────
     #offres et #atelier ne sont pas des sections classiques : ce sont deux
     moments de la timeline de .ds. On vise donc la bonne progression du
     scroll plutôt que le haut de l'élément (sinon le lien semble mort). */
  (function(){
    var ds = document.querySelector(".ds");
    /* CORRECTIF 03/09 — la branche mobile visait getBoundingClientRect() de
       #ofStage, qui est en position:absolute DANS un conteneur sticky : son
       rect avant épinglage vaut le HAUT de la scène, c'est-à-dire la
       progression 0. À cet instant les offres sont encore à opacity:0 et
       l'écran affiche la photo du bureau + « Ce qui compte ne se voit pas tout
       de suite ». Le visiteur demandait un prix et recevait un aphorisme.
       On vise désormais la progression sur mobile aussi, au MILIEU de la
       fenêtre où les cartes sont visibles et cliquables (is-live .30→.56 pour
       les offres, p>.64 pour l'atelier), pas sur son bord. */
    var reperesPC  = { offres: .46, atelier: .84 };
    var reperesTel = { offres: .42, atelier: .78 };
    document.addEventListener("click", function(e){
      var a = e.target && e.target.closest ? e.target.closest('a[href^="#"]') : null;
      if (!a) return;
      var cle = a.getAttribute("href").slice(1);
      if (!(cle in reperesPC)) return;
      e.preventDefault();
      var cible;
      if (ds){
        var reperes = matchMedia("(max-width:960px)").matches ? reperesTel : reperesPC;
        cible = ds.offsetTop + Math.max(0, ds.offsetHeight - innerHeight) * reperes[cle];
      } else {
        var el = document.getElementById(cle === "offres" ? "ofStage" : "atStage");
        cible = el ? el.getBoundingClientRect().top + (window.pageYOffset || 0) - 90 : 0;
      }
      if (typeof lenis !== "undefined" && lenis && lenis.scrollTo) lenis.scrollTo(cible, { duration: 1.7 });
      else scrollTo({ top: cible, behavior: "smooth" });
    });
  })();
})();

</script>

<script>
/* ── La descente de l'arbre ──────────────────────────────────
   L'image de l'arbre se déplace du haut vers le bas de son propre corps au
   fil du défilement de la page : en haut on est dans le houppier, en bas au
   pied du tronc. Avec GSAP quand il est là, à la main sinon — et jamais sous
   « réduire les animations », où l'arbre reste simplement posé sur le tronc. */
(function(){
  var arbre = document.querySelector('.lae-colonne__arbre');
  if (!arbre) return;

  var reduit = !!(window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches);
  if (reduit) { arbre.style.transform = 'translate3d(-50%,-38%,0)'; return; }

  function course(){ return Math.max(0, arbre.offsetHeight - innerHeight); }

  var G = window.gsap, ST = window.ScrollTrigger;
  if (G && ST) {
    G.to(arbre, {
      y: function(){ return -course(); },
      ease: 'none',
      scrollTrigger: { trigger: document.body, start: 'top top', end: 'bottom bottom',
                       scrub: .6, invalidateOnRefresh: true }
    });
    return;
  }

  /* Repli sans GSAP : on suit le défilement à la main. */
  var tick = false;
  function place(){
    var max = Math.max(1, document.body.scrollHeight - innerHeight);
    var p = Math.min(1, Math.max(0, (window.pageYOffset || 0) / max));
    arbre.style.transform = 'translate3d(-50%,' + (-course() * p) + 'px,0)';
    tick = false;
  }
  addEventListener('scroll', function(){ if (!tick) { tick = true; requestAnimationFrame(place); } }, { passive: true });
  addEventListener('resize', place);
  place();
})();
</script>

<?php
get_footer();
