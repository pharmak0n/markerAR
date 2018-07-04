<!doctype html>
<html lang="it">
<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name='viewport' content='width=device-width, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0'>
  <title>#seguiloscoiattolo - Reader Marker #3</title>
  <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png?v=dLmNQqjyQ0">
  <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png?v=dLmNQqjyQ0">
  <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png?v=dLmNQqjyQ0">
  <link rel="manifest" href="/site.webmanifest?v=dLmNQqjyQ0">
  <link rel="mask-icon" href="/safari-pinned-tab.svg?v=dLmNQqjyQ0" color="#5bbad5">
  <link rel="shortcut icon" href="/favicon.ico?v=dLmNQqjyQ0">
  <meta name="apple-mobile-web-app-title" content="#seguiloscoiattolo">
  <meta name="application-name" content="#seguiloscoiattolo">
  <meta name="msapplication-TileColor" content="#da532c">
  <meta name="theme-color" content="#ffffff">

  <link rel="stylesheet" href="../style.css" type="text/css" media="all">

  <!-- three.js library -->
  <script src='../data/vendor/three.min93.js'></script>
  <script src='../data/vendor/Detector.js'></script>

  <script src="../data/vendor/GLTFLoader.js"></script>
  <!-- ar.js -->
  <script src="../data/vendor/ar.js"></script>
  <script>THREEx.ArToolkitContext.baseURL = '../data/assets/'</script>

  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-121328164-1"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-121328164-1');
  </script>

  <body>
    <div id="pacmanBox">
      <p>Visualizzatore per la realtà aumentata in fase di caricamento.</p>
      <p>Se riscontri problemi ricarica la pagina.</p>
      <p id="loading"></p>

      <div class="pacman-wrapper"><div class="pacman"><div></div><div></div><div></div><div></div><div></div></div></div>
    </div>

    <div style="position: absolute; top: 0; width:100%; text-align: center;z-index:1">
    <script>

    if (Detector.webgl) {
        // Initiate function or other initializations here
        start();
    } else {
        var warning = Detector.getWebGLErrorMessage();
        document.getElementById('container').appendChild(warning);
    }

    function start() {
      //////////////////////////////////////////////////////////////////////////////////
      //		Init
      //////////////////////////////////////////////////////////////////////////////////

      // init renderer
      var renderer	= new THREE.WebGLRenderer({
        antialias	: true,
        alpha: true
      });
      renderer.setClearColor(new THREE.Color('lightgrey'), 0)
      renderer.setPixelRatio(window.devicePixelRatio);
      renderer.setSize( window.innerWidth, window.innerHeight );
      renderer.domElement.style.position = 'absolute'
      renderer.domElement.style.top = '0px'
      renderer.domElement.style.left = '0px'
      document.body.appendChild( renderer.domElement );

      // array of functions for the rendering loop
      var onRenderFcts= [];

      // init scene and camera
      var scene	= new THREE.Scene();

      //////////////////////////////////////////////////////////////////////////////////
      //		Initialize a basic camera
      //////////////////////////////////////////////////////////////////////////////////

      // Create a camera
      var camera = new THREE.Camera();
      scene.add(camera);

      ////////////////////////////////////////////////////////////////////////////////
      //          handle arToolkitSource
      ////////////////////////////////////////////////////////////////////////////////

      var artoolkitProfile = new THREEx.ArToolkitProfile()
      artoolkitProfile.sourceWebcam()
      //artoolkitProfile.sourceImage(THREEx.ArToolkitContext.baseURL + 'marker.png')

      var arToolkitSource = new THREEx.ArToolkitSource(artoolkitProfile.sourceParameters)

      arToolkitSource.init(function onReady(){
        onResize()
      })

      // handle resize
      window.addEventListener('resize', function(){
        onResize()
      })
      function onResize(){
        arToolkitSource.onResizeElement()
        arToolkitSource.copyElementSizeTo(renderer.domElement)
        if( arToolkitContext.arController !== null ){
          arToolkitSource.copyElementSizeTo(arToolkitContext.arController.canvas)
        }
      }

      ////////////////////////////////////////////////////////////////////////////////
      //          initialize arToolkitContext
      ////////////////////////////////////////////////////////////////////////////////

      // set patternRatio
      artoolkitProfile.contextParameters.patternRatio = 0.8
      artoolkitProfile.contextParameters.cameraParametersUrl = THREEx.ArToolkitContext.baseURL + 'data/camera_para.dat'
      artoolkitProfile.contextParameters.detectionMode = 'color'

      // create atToolkitContext
      var arToolkitContext = new THREEx.ArToolkitContext(artoolkitProfile.contextParameters)

      // initialize it
      arToolkitContext.init(function onCompleted(){
        // copy projection matrix to camera
        camera.projectionMatrix.copy( arToolkitContext.getProjectionMatrix() );
      })

      // update artoolkit on every frame
      onRenderFcts.push(function(){
        if( arToolkitSource.ready === false )	return

        arToolkitContext.update( arToolkitSource.domElement )
      })


      ////////////////////////////////////////////////////////////////////////////////
      //          Create a ArMarkerControls
      ////////////////////////////////////////////////////////////////////////////////

      var markerGroup = new THREE.Group
      scene.add(markerGroup)
      var markerControls = new THREEx.ArMarkerControls(arToolkitContext, markerGroup, {
        type : 'pattern',
        // patternUrl : THREEx.ArToolkitContext.baseURL + '../data/data/patt.hiro',
        patternUrl : THREEx.ArToolkitContext.baseURL + '03_pattern_80_w.patt',
      })


      // build a smoothedControls
      var smoothedGroup = new THREE.Group()
      scene.add(smoothedGroup)
      var smoothedControls = new THREEx.ArSmoothedControls(smoothedGroup)
      onRenderFcts.push(function(delta){
        smoothedControls.update(markerGroup)
      })

      //////////////////////////////////////////////////////////////////////////////////
      //		add an object in the scene
      //////////////////////////////////////////////////////////////////////////////////

      var markerScene = new THREE.Scene()
      smoothedGroup.add(markerScene)

      ambient = new THREE.AmbientLight(0xffffff, 0.75);
      markerScene.add(ambient);

      //Create a PointLight and turn on shadows for the light
      pointLight = new THREE.PointLight( 0xffffff, 0.6, 75000, 10 );
      pointLight.position.set( 500, -30, 4000 );
      pointLight.castShadow = true;            // default false

      //Set up shadow properties for the light
      pointLight.shadow.mapSize.width = 1024;  // default
      pointLight.shadow.mapSize.height = 1024; // default
      pointLight.shadow.camera.near = 1;       // default
      pointLight.shadow.camera.far = 200;      // default
      markerScene.add( pointLight );
      //
      // //Create a SpotLight
      // spotLight = new THREE.SpotLight( 0xffffff, 10 );
      // spotLight.position.set( 100, -125, 200 );
      // spotLight.angle = Math.PI / 3;
      // spotLight.penumbra = 0.5;
      // spotLight.decay = 40;
      // spotLight.distance = 1500;
      // spotLight.castShadow = true;
      // spotLight.shadow.mapSize.width = 1024;
      // spotLight.shadow.mapSize.height = 1024;
      // spotLight.shadow.camera.near = 1;
      // spotLight.shadow.camera.far = 200;
      // //scene.add( spotLight );
      //
      // //Create a SpotLight
      // spotLight2 = new THREE.SpotLight( 0xffffff, 10);
      // spotLight2.position.set( 20, -20, 360 );
      // spotLight2.angle = Math.PI / 3;
      // spotLight2.penumbra = 0.5;
      // spotLight2.decay = 20;
      // spotLight2.distance = 1500;
      // spotLight2.castShadow = true;
      // spotLight2.shadow.mapSize.width = 1024;
      // spotLight2.shadow.mapSize.height = 1024;
      // spotLight2.shadow.camera.near = 1;
      // spotLight2.shadow.camera.far = 200;
      // markerScene.add( spotLight2 );
      //
      // //Create a PointLight and turn on shadows for the light
      // pointLightBack = new THREE.PointLight( 0xffffff, 0.7, 20000, 2);
      // pointLightBack.position.set( 500, 700, -2500 );
      // pointLightBack.castShadow = false;            // default false
      //
      // //Set up shadow properties for the light
      // pointLightBack.shadow.mapSize.width = 1024;  // default
      // pointLightBack.shadow.mapSize.height = 1024; // default
      // pointLightBack.shadow.camera.near = 1;       // default
      // pointLightBack.shadow.camera.far = 500;      // default
      // markerScene.add( pointLightBack );
      //
      // //Create a PointLight and turn on shadows for the light
      // pointLightCenter = new THREE.PointLight( 0xffffff, 0.1, 10000, 20);
      // pointLightCenter.position.set( 0, 0, 0 );
      // pointLightCenter.castShadow = false;            // default false
      //
      // //Set up shadow properties for the light
      // pointLightCenter.shadow.mapSize.width = 1024;  // default
      // pointLightCenter.shadow.mapSize.height = 1024; // default
      // pointLightCenter.shadow.camera.near = 1;       // default
      // pointLightCenter.shadow.camera.far = 500;      // default
      // markerScene.add( pointLightCenter );

      // model
      var model  = new THREE.Object3D();
      markerScene.add(model);

      var loader = new THREE.GLTFLoader();

      // Load a glTF resource
      loader.load(
        // resource URL
        '../data/models/03/scene.gltf',

        // called when the resource is loaded
        function ( gltf ) {
          document.getElementById('pacmanBox').style.display = "none";

          gltf.scene.rotateX(-Math.PI/2);
          gltf.scene.scale.set(0.015,0.015,0.015);
          gltf.scene.translateY(-1);
          model.add( gltf.scene );

          gltf.animations; // Array<THREE.AnimationClip>
          gltf.scene; // THREE.Scene
          gltf.scenes; // Array<THREE.Scene>
          gltf.cameras; // Array<THREE.Camera>
          gltf.asset; // Object

        },
        // called when loading is in progresses
        function ( xhr ) {
          console.log( ( xhr.loaded / xhr.total * 100 ) + '% loaded' );
          document.getElementById('loading').innerHTML = ( xhr.loaded / xhr.total * 100 ) + '%';
        },
        // called when loading has errors
        function ( error ) {
          console.log( 'An error happened' );
        }
      );

      // var geometry	= new THREE.TorusKnotGeometry(0.3,0.1,64,16);
      // var material	= new THREE.MeshNormalMaterial();
      // var mesh	= new THREE.Mesh( geometry, material );
      // mesh.position.y	= 0.5
      // markerScene.add( mesh );

      onRenderFcts.push(function(delta){
        //objLoader.rotation.x += delta * Math.PI
        //mesh.rotation.x += delta * Math.PI
      })

      //////////////////////////////////////////////////////////////////////////////////
      //		render the whole thing on the page
      //////////////////////////////////////////////////////////////////////////////////
      // render the scene
      onRenderFcts.push(function(){
        renderer.render( scene, camera );
      })

      // run the rendering loop
      var lastTimeMsec= null
      requestAnimationFrame(function animate(nowMsec){
        // keep looping
        requestAnimationFrame( animate );

        model.rotateZ(0.02);
        // measure time
        lastTimeMsec	= lastTimeMsec || nowMsec-1000/60
        var deltaMsec	= Math.min(200, nowMsec - lastTimeMsec)
        lastTimeMsec	= nowMsec
        // call each update function
        onRenderFcts.forEach(function(onRenderFct){
          onRenderFct(deltaMsec/1000, nowMsec/1000)
        })
      })

    };

  </script>
</body>
