"""
Boucle vidéo de fond : une canopée vue d'en bas, agitée par le vent.

Bouclable : tout le mouvement est piloté par des sinusoïdes qui font un
tour complet sur la durée du plan, donc la dernière image enchaîne sur la
première sans saut.
"""
import bpy, addon_utils, math, os

addon_utils.enable("add_curve_sapling", default_set=True)

bpy.ops.object.select_all(action='SELECT')
bpy.ops.object.delete(use_global=False)
for bloc in (bpy.data.meshes, bpy.data.curves, bpy.data.materials):
    for x in list(bloc):
        bloc.remove(x)

IMAGES = int(os.environ.get("LAE_IMAGES", "120"))   # durée de la boucle
W = int(os.environ.get("LAE_W", "960"))
H = int(os.environ.get("LAE_H", "540"))

def matiere(nom, couleur, rugosite=0.8):
    m = bpy.data.materials.new(nom); m.use_nodes = True
    p = m.node_tree.nodes["Principled BSDF"]
    p.inputs["Base Color"].default_value = couleur
    p.inputs["Roughness"].default_value = rugosite
    return m

bois    = matiere("bois",    (0.045, 0.085, 0.05, 1.0), 0.93)
feuille = matiere("feuille", (0.05,  0.20,  0.075, 1.0), 0.6)

def objet(prefixe):
    for o in bpy.data.objects:
        if o.name.lower().startswith(prefixe):
            return o

# ── trois houppiers à des profondeurs différentes : la parallaxe fait le relief ──
couches = []
for i, (graine, y, ech) in enumerate(((21, 0.0, 1.0), (9, 6.0, 0.72), (34, -5.5, 1.25))):
    bpy.ops.curve.tree_add(
        do_update=True, chooseSet='0', bevel=True, prune=False,
        showLeaves=True, useArm=False, bevelRes=1, resU=2,
        levels=3,
        length=(0.9, 0.36, 0.44, 0.4), branches=(0, 40, 20, 8),
        curveRes=(8, 6, 4, 3), curve=(0, -30, -20, 0), curveV=(30, 60, 60, 0),
        baseSplits=1, segSplits=(0.2, 0.25, 0.2, 0.0),
        ratio=0.014, ratioPower=1.25, scale=16.0,
        downAngle=(90, 50, 42, 40), downAngleV=(0, 36, 20, 18),
        baseSize=0.28, taper=(1, 1, 1, 1),
        leaves=40, leafScale=0.24, leafScaleX=0.6, leafShape='hex', leafDist='6',
        shape='7', seed=graine,
    )
    tronc = objet("tree"); tronc.name = f"houppier{i}"
    tronc.data.materials.append(bois)
    f = objet("leaves")
    if f:
        f.data.materials.clear(); f.data.materials.append(feuille)
        f.name = f"feuillage{i}"
    for o in (tronc, f):
        if o:
            o.location = (0, y, 0)
            o.scale = (ech, ech, ech)
    couches.append((tronc, f, i))

# ── caméra sous l'arbre, tournée vers le ciel ──
# X=180° : l'objectif regarde vers le haut (par défaut il regarde vers le bas).
bpy.ops.object.camera_add(location=(1.5, -0.7, 7.4), rotation=(math.radians(171), 0, math.radians(-8)))
cam = objet("camera")
cam.data.lens = 13
bpy.context.scene.camera = cam

# ── la lumière traverse le feuillage ──
bpy.ops.object.light_add(type='SUN', location=(4, 3, 26))
soleil = objet("sun")
soleil.data.energy = 9.0
soleil.data.angle = math.radians(3)
soleil.data.color = (1.0, 0.98, 0.78)
soleil.rotation_euler = (math.radians(16), 0, math.radians(30))

monde = bpy.context.scene.world
monde.use_nodes = True
monde.node_tree.nodes["Background"].inputs[0].default_value = (0.10, 0.26, 0.16, 1.0)
monde.node_tree.nodes["Background"].inputs[1].default_value = 2.6

# ── le vent : rotations sinusoïdales, un tour complet sur la boucle ──
for tronc, f, i in couches:
    for o in (tronc, f):
        if not o:
            continue
        base = list(o.rotation_euler)
        for img in range(IMAGES + 1):
            t = img / IMAGES
            a = math.sin(2 * math.pi * t + i * 1.7) * math.radians(1.5 + i * 0.4)
            b = math.sin(4 * math.pi * t + i * 0.9) * math.radians(0.9)
            o.rotation_euler = (base[0] + a, base[1] + b, base[2])
            o.keyframe_insert("rotation_euler", frame=img + 1)
        for fc in o.animation_data.action.fcurves:
            for kp in fc.keyframe_points:
                kp.interpolation = 'BEZIER'

# la caméra dérive doucement et revient à son point de départ
base_rot = list(cam.rotation_euler)
base_loc = list(cam.location)
for img in range(IMAGES + 1):
    t = img / IMAGES
    cam.rotation_euler = (base_rot[0] + math.sin(2*math.pi*t) * math.radians(1.1),
                          base_rot[1],
                          base_rot[2] + math.cos(2*math.pi*t) * math.radians(1.2))
    cam.location = (base_loc[0] + math.sin(2*math.pi*t) * 0.22, base_loc[1] + math.cos(2*math.pi*t) * 0.14, base_loc[2])
    cam.keyframe_insert("rotation_euler", frame=img + 1)
    cam.keyframe_insert("location", frame=img + 1)

sc = bpy.context.scene
sc.frame_start = 1
sc.frame_end = IMAGES          # l'image IMAGES+1 est identique à la 1re : on l'exclut
sc.render.fps = 24
sc.render.engine = 'CYCLES'
sc.cycles.device = 'CPU'
sc.cycles.samples = int(os.environ.get("LAE_SAMPLES", "10"))
sc.cycles.use_denoising = False
sc.cycles.max_bounces = 2
sc.render.resolution_x = W
sc.render.resolution_y = H
sc.render.image_settings.file_format = 'PNG'
sc.render.filepath = os.environ.get("LAE_SORTIE", "/home/claude/arbre/img/i")
bpy.ops.render.render(animation=True)
print("BOUCLE OK")
