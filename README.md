# markerAR
This repo contains 5 AR markers and scene made for the treasure hunt on telegram #seguiloscoiattolo (www.seguiloscoiattolo.com).

The example are based on:
* ar.js - https://github.com/jeromeetienne/AR.js
* three.js - https://threejs.org/

The 3d models are loaded via file.gltf
* three.js docs - https://threejs.org/docs/#examples/loaders/GLTFLoader
* glTF-Blender-Exporter - https://github.com/KhronosGroup/glTF-Blender-Exporter

Additional three.js module for WebGL-compatibility-check
* https://threejs.org/docs/#manual/introduction/WebGL-compatibility-check

The AR marker are made this way:
1. Create the image 500x500px. Some information refers to use a background rgb(240,240,240) and a simple shape as "visual trigger". I made some tests and it seems that the only important thing is the contrast between the background and the shape. In this repo the marker are made with background rgb(240,240,240), feel free to test with colors.
2. Upload the image on the AR marker generator available at:
https://jeromeetienne.github.io/AR.js/three.js/examples/marker-training/examples/generator.html
3. Set the pattern ratio to 0.8
4. Download the marker image, the .patt and the pdf files.
5. Place the .patt file in markerAR/data/assets/
6. The 3d models are made with cinema4d > export to obj/mtl > uploaded on sketchfab.com > made public > downloaded as .gltf > placed in markerAR/data/models/
7. Edit here to link .patt file
https://github.com/pharmak0n/markerAR/blob/a97987b51a5357a51db11009ef00e187844e0814/ar1623/index.php#L152
8. Edit index.php at the following line to link the 3d model
https://github.com/pharmak0n/markerAR/blob/a97987b51a5357a51db11009ef00e187844e0814/ar1623/index.php#L245

*You can edit the patternRatio at the following line (you have to recreate the .patt file via online generator)
https://github.com/pharmak0n/markerAR/blob/a97987b51a5357a51db11009ef00e187844e0814/ar1623/index.php#L122
