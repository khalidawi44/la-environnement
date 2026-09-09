"""
Arbre en colonne pour L.A Environnement.
Rendu vertical : cime en haut, tronc, racines en bas — l'image que l'on
descend en défilant sur la page d'accueil.
"""
import bpy, addon_utils, math, sys, os

addon_utils.enable("add_curve_sapling", default_set=True)

# ── scène nette ──
bpy.ops.object.select_all(action='SELECT')
bpy.ops.object.delete(use_global=False)
for bloc in (bpy.data.meshes, bpy.data.curves, bpy.data.materials):
    for x in list(bloc):
        bloc.remove(x)

TAILLE = float(os.environ.get("LAE_ECHELLE", "1.0"))

def matiere(nom, couleur, rugosite=0.85, transmission=0.0):
    m = bpy.data.materials.new(nom)
    m.use_nodes = True
    p = m.node_tree.nodes["Principled BSDF"]
    p.inputs["Base Color"].default_value = couleur
    p.inputs["Roughness"].default_value = rugosite
    if transmission and "Transmission" in p.inputs:
        p.inputs["Transmission"].default_value = transmission
    return m

bois    = matiere("bois",    (0.055, 0.105, 0.062, 1.0), 0.92)
feuille = matiere("feuille", (0.055, 0.215, 0.082, 1.0), 0.66)

# ── l'arbre ──
bpy.ops.curve.tree_add(
    do_update=True, chooseSet='0', bevel=True, prune=False,
    showLeaves=True, useArm=False,
    handleType='0', bevelRes=2, resU=3,
    levels=3,
    length=(0.92, 0.34, 0.42, 0.42),
    lengthV=(0.0, 0.12, 0.12, 0.0),
    branches=(0, 46, 22, 10),
    curveRes=(10, 8, 5, 3),
    curve=(0, -34, -22, 0), curveV=(28, 60, 66, 0),
    baseSplits=1, segSplits=(0.22, 0.28, 0.22, 0.0),
    splitAngle=(16, 20, 22, 0), splitAngleV=(6, 8, 8, 0),
    ratio=0.0135, ratioPower=1.28,
    scale=18.0, scaleV=0.0,
    downAngle=(90, 52, 44, 42), downAngleV=(0, 38, 22, 20),
    rotate=(99.5, 137.5, 137.5, 137.5), rotateV=(15, 0, 0, 0),
    baseSize=0.62, baseSize_s=0.14,
    taper=(1, 1, 1, 1), taperCrown=0.42,
    leaves=46, leafScale=0.26, leafScaleX=0.62, leafShape='hex',
    leafDist='6', attractUp=(0.0, -0.4, -0.6, 0.0),
    shape='7', customShape=(0.62, 1.0, 0.42, 0.62),
    seed=21,
)
# En mode fond, bpy.context.object n'est pas remis à jour par l'opérateur :
# on retrouve les objets créés par leur nom.
def objet(prefixe):
    for o in bpy.data.objects:
        if o.name.lower().startswith(prefixe):
            return o
    return None

arbre = objet("tree")
if arbre is None:
    raise SystemExit("Sapling n'a pas créé d'arbre : " + str([o.name for o in bpy.data.objects]))
arbre.name = "arbre"
arbre.data.materials.append(bois)

feuilles = objet("leaves")
if feuilles is not None:
    feuilles.data.materials.clear()
    feuilles.data.materials.append(feuille)

# ── les racines : un second arbre retourné, tassé au pied du tronc ──
bpy.ops.curve.tree_add(
    do_update=True, chooseSet='0', bevel=True, prune=False,
    showLeaves=False, useArm=False, bevelRes=2, resU=3,
    levels=2,
    length=(0.42, 0.44, 0.4, 0.4),
    branches=(0, 26, 12, 6),
    curveRes=(6, 5, 3, 2),
    curve=(0, -46, -30, 0), curveV=(40, 70, 60, 0),
    baseSplits=2, segSplits=(0.4, 0.3, 0.0, 0.0),
    ratio=0.02, ratioPower=1.1,
    scale=6.0, scaleV=0.0,
    downAngle=(90, 96, 88, 80), downAngleV=(0, 26, 20, 16),
    baseSize=0.05, taper=(1, 1, 1, 1),
    shape='4', seed=7,
)
racines = objet("tree")
if racines is None:
    raise SystemExit("Sapling n'a pas créé les racines")
racines.name = "racines"
racines.data.materials.append(bois)
racines.rotation_euler = (math.radians(180), 0, 0)
racines.scale = (3.0, 3.0, 1.6)
racines.location = (0, 0, 0.15)

# ── cadrage : caméra orthographique, de face, l'arbre entier ──
bpy.ops.object.camera_add(location=(0, -40, float(os.environ.get('LAE_CAMZ', '11.4'))), rotation=(math.radians(90), 0, 0))
cam = objet("camera")
cam.data.type = 'ORTHO'
cam.data.ortho_scale = float(os.environ.get('LAE_ORTHO', '27.0'))
bpy.context.scene.camera = cam

# ── lumière : un soleil rasant derrière, un remplissage doux devant ──
bpy.ops.object.light_add(type='SUN', location=(6, 14, 22))
soleil = objet("sun")
soleil.data.energy = 5.5
soleil.data.angle = math.radians(6)
soleil.data.color = (0.85, 1.0, 0.72)
soleil.rotation_euler = (math.radians(46), 0, math.radians(210))

bpy.ops.object.light_add(type='SUN', location=(-10, -18, 10))
appoint = [o for o in bpy.data.objects if o.type == 'LIGHT'][-1]
appoint.data.energy = 1.6
appoint.data.color = (0.62, 0.85, 0.7)
appoint.rotation_euler = (math.radians(66), 0, math.radians(-30))

# ── rendu : fond transparent, la page passe derrière ──
sc = bpy.context.scene
sc.render.engine = 'CYCLES'
sc.cycles.device = 'CPU'
sc.cycles.samples = int(os.environ.get("LAE_SAMPLES", "24"))
sc.cycles.use_denoising = False  # ce build de Blender est sans OpenImageDenoise
sc.cycles.max_bounces = 4
sc.cycles.transparent_max_bounces = 4
sc.render.film_transparent = True
sc.render.image_settings.file_format = 'PNG'
sc.render.image_settings.color_mode = 'RGBA'
sc.render.resolution_x = int(os.environ.get("LAE_W", "600"))
sc.render.resolution_y = int(os.environ.get("LAE_H", "3000"))
sc.render.resolution_percentage = 100
sc.render.filepath = os.environ.get("LAE_SORTIE", "/home/claude/arbre/arbre")

bpy.ops.render.render(write_still=True)
print("RENDU OK", sc.render.filepath)
