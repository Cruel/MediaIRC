var bgcanvas;

var delta = [ 0, 0 ];
var stage = [ window.screenX, window.screenY, window.innerWidth, window.innerHeight - 60 ];
getBrowserDimensions();

var worldAABB, world, iterations = 1, timeStep = 1 / 15;

var walls = [];
var wall_thickness = 200;
var wallsSetted = false;

var bodies, elements;

var createMode = false;
var destroyMode = false;

var isMouseDown = false;
var mouseJoint;
var mouse = { x: 0, y: 0 };
var gravity = { x: 0, y: 0 };

var PI2 = Math.PI * 2;

var timeOfLastTouch = 0;

var $header;

$(function(){
	init();
	play();
});

function init() {
	$header = $('#header');
	bgcanvas = $('#canvas')[0];

	document.onmousedown = onDocumentMouseDown;
	document.onmouseup = onDocumentMouseUp;
	document.onmousemove = onDocumentMouseMove;
	document.ondblclick = onDocumentDoubleClick;

	document.addEventListener( 'touchstart', onDocumentTouchStart, false );
	document.addEventListener( 'touchmove', onDocumentTouchMove, false );
	document.addEventListener( 'touchend', onDocumentTouchEnd, false );

	window.addEventListener( 'deviceorientation', onWindowDeviceOrientation, false );

	// init box2d
	worldAABB = new b2AABB();
	worldAABB.minVertex.Set( -200, -200 );
	worldAABB.maxVertex.Set( window.innerWidth + 200, window.innerHeight + 200 );

	world = new b2World( worldAABB, new b2Vec2( 0, 0 ), true );

	setWalls();
	reset();
}


function play() {
	setInterval( loop, 1000 / 40 );
}

function reset() {
	var i;
	if ( bodies ) {
		for ( i = 0; i < bodies.length; i++ ) {
			var body = bodies[ i ]
			bgcanvas.removeChild( body.GetUserData().element );
			world.DestroyBody( body );
			body = null;
		}
	}

	bodies = [];
	elements = [];

	createWelcomeBall();
	fetchBalls();
}

//

function onDocumentMouseDown() {
	isMouseDown = true;
//	return false;
}

function onDocumentMouseUp() {
	isMouseDown = false;
//	return false;
}

function onDocumentMouseMove( event ) {
	mouse.x = event.clientX;
	mouse.y = event.clientY;
}

function onDocumentDoubleClick() {
	var body = getBodyAtMouse();
	if (body) {
		console.log(body.m_userData);
	}
//	reset();
}

function onDocumentTouchStart( event ) {
	if( event.touches.length == 1 ) {
		event.preventDefault();
		// Faking double click for touch devices
		var now = new Date().getTime();
		if ( now - timeOfLastTouch  < 250 ) {
			reset();
			return;
		}
		timeOfLastTouch = now;
		mouse.x = event.touches[ 0 ].pageX;
		mouse.y = event.touches[ 0 ].pageY;
		isMouseDown = true;
	}
}

function onDocumentTouchMove( event ) {
	if ( event.touches.length == 1 ) {
		event.preventDefault();
		mouse.x = event.touches[ 0 ].pageX;
		mouse.y = event.touches[ 0 ].pageY;
	}
}

function onDocumentTouchEnd( event ) {
	if ( event.touches.length == 0 ) {
		event.preventDefault();
		isMouseDown = false;
	}
}

function onWindowDeviceOrientation( event ) {
	if ( event.beta ) {
		gravity.x = Math.sin( event.gamma * Math.PI / 180 );
		gravity.y = Math.sin( ( Math.PI / 4 ) + event.beta * Math.PI / 180 );
	}
}

function fetchBalls(){
	$.get('/balls', null, function(data){
		var matched;
		for (i in data){
			matched = false;
			for (x in bodies){
				var $div = $(bodies[x].m_userData.element);
				if ($div.data('channel') == data[i].channel && $div.data('host') == data[i].host){
					matched = true;
					break;
				}
			}
			if (!matched) {
				var ball = createBall(data[i].channel, 150);
				$(ball).data('host',data[i].host).data('channel',data[i].channel);
			}
		}
		console.log(data);
		setTimeout(fetchBalls, 20000);
	},'json');
}

//

function createWelcomeBall() {
	var size = 250;
	var element = createBall("", size);
	var canvas = element.children[0];

	with (canvas.getContext("2d")){
	    textAlign = "center";
	    font = '35px Arial';
	    fillText("Want Balls?", 125, 75);
	    font = '18px Arial';
	    fillText("Send a bot to your channel", 125, 110);
	}
}

function stringToColor(s) {
	var colors = ['brown','burlywood','cadetblue','chocolate','cyan','darkcyan','darkorange','darksalmon','goldenrod','greenyellow','hotpink','lightpink','lightskyblue','orangered','orange','pink','red','skyblue','yellow'];
	var hash = hashStr(s);
	var index = hash % colors.length;
	return colors[index];
}
function hashStr(str) {
	var hash = 0;
	for (var i = 0; i < str.length; i++) {
		var charCode = str.charCodeAt(i);
		hash += charCode;
	}
	return hash;
}

function createBall(text, size, x, y) {

	var x = x || Math.random() * stage[2];
	var y = y || Math.random() * 200 + 200;
	
	var strokeWidth = 3;
	
	var div = document.createElement( 'div' );
	div.width = size + strokeWidth*2;
	div.height = size + strokeWidth*2;	
	div.style.position = 'absolute';
	div.style.left = -2000 + 'px';
	div.style.top = -2000 + 'px';
	div.style.cursor = "default";
	
	bgcanvas.appendChild(div);
	elements.push(div);
	
	var canvas = document.createElement("canvas");
	canvas.width = div.width;
	canvas.height = div.height;
	canvas.style.position = 'relative';

	with (canvas.getContext("2d")){
		translate(strokeWidth, strokeWidth);
		
		beginPath();
	    arc(size * .5, size * .5, size * .5, 0, PI2, true);
	    closePath();
	    
	    save();
	    fillStyle = stringToColor(text);
	    fill();
	    restore();
	    
	    lineWidth = 3;
	    strokeStyle = '#003300';
	    stroke();
	    
	    save();
	    translate(size/2, size/2);
	    rotate(Math.PI / 4);
	    textAlign = "center";
	    font = (size*0.9/text.length)+'pt Arial';
	    fillText(text, 0,0);
	    restore();
	}
	
	div.appendChild(canvas);

	var b2body = new b2BodyDef();

	var circle = new b2CircleDef();
	circle.radius = size >> 1;
	circle.density = 1;
	circle.friction = 0.3;
	circle.restitution = 0.3;
	b2body.AddShape(circle);
	b2body.userData = { element: div };

	b2body.position.Set( x, y );
	b2body.linearVelocity.Set( Math.random() * 400 - 200, Math.random() * 400 - 200 );
	bodies.push( world.CreateBody(b2body) );
	
	return div;
}


function loop() {

	if (getBrowserDimensions()) {
		setWalls();
	}

	delta[0] += (0 - delta[0]) * .5;
	delta[1] += (0 - delta[1]) * .5;

	world.m_gravity.x = gravity.x * 350 + delta[0];
	world.m_gravity.y = gravity.y * 350 + delta[1];

	mouseDrag();
	world.Step(timeStep, iterations);

	for (var i = 0; i < bodies.length; i++) {
		var body = bodies[i];
		var element = elements[i];

		element.style.left = (body.m_position0.x - (element.width >> 1)) + 'px';
		element.style.top = (body.m_position0.y - (element.height >> 1)) + 'px';

		var style = 'rotate(' + (body.m_rotation0 * 57.2957795) + 'deg) translateZ(0)';
		element.style.WebkitTransform = style;
		element.style.MozTransform = style;
		element.style.OTransform = style;
		element.style.msTransform = style;
		element.style.transform = style;
	}
}


// .. BOX2D UTILS

function createBox(world, x, y, width, height, fixed) {
	if (typeof(fixed) == 'undefined') {
		fixed = true;
	}

	var boxSd = new b2BoxDef();

	if (!fixed) {
		boxSd.density = 1.0;
	}
	boxSd.extents.Set(width, height);

	var boxBd = new b2BodyDef();
	boxBd.AddShape(boxSd);
	boxBd.position.Set(x,y);

	return world.CreateBody(boxBd);
}

function mouseDrag() {
	// mouse press
	if (createMode) {
//		createBall( mouse.x, mouse.y );
	} else if (isMouseDown && !mouseJoint) {
		var body = getBodyAtMouse();
		if (body) {
			var md = new b2MouseJointDef();
			md.body1 = world.m_groundBody;
			md.body2 = body;
			md.target.Set(mouse.x, mouse.y);
			md.maxForce = 30000 * body.m_mass;
			// md.timeStep = timeStep;
			mouseJoint = world.CreateJoint(md);
			body.WakeUp();
		} else {
			createMode = true;
		}
	}

	// mouse release
	if (!isMouseDown) {
		createMode = false;
		destroyMode = false;

		if (mouseJoint) {
			world.DestroyJoint(mouseJoint);
			mouseJoint = null;
		}
	}

	// mouse move
	if (mouseJoint) {
		var p2 = new b2Vec2(mouse.x, mouse.y);
		mouseJoint.SetTarget(p2);
	}
}

function getBodyAtMouse() {
	// Make a small box.
	var mousePVec = new b2Vec2();
	mousePVec.Set(mouse.x, mouse.y);

	var aabb = new b2AABB();
	aabb.minVertex.Set(mouse.x - 1, mouse.y - 1);
	aabb.maxVertex.Set(mouse.x + 1, mouse.y + 1);

	// Query the world for overlapping shapes.
	var k_maxCount = 10;
	var shapes = new Array();
	var count = world.Query(aabb, shapes, k_maxCount);
	var body = null;

	for (var i = 0; i < count; ++i) {
		if (shapes[i].m_body.IsStatic() == false) {
			if ( shapes[i].TestPoint(mousePVec) ) {
				body = shapes[i].m_body;
				break;
			}
		}
	}
	return body;
}

function setWalls() {
	if (wallsSetted) {
		world.DestroyBody(walls[0]);
		world.DestroyBody(walls[1]);
		world.DestroyBody(walls[2]);
		world.DestroyBody(walls[3]);
		world.DestroyBody(walls[4]);
		walls[0] = null; 
		walls[1] = null;
		walls[2] = null;
		walls[3] = null;
		walls[4] = null;
	}

	walls[0] = createBox(world, stage[2] / 2, - wall_thickness, stage[2], wall_thickness);
	walls[1] = createBox(world, stage[2] / 2, stage[3] + wall_thickness, stage[2], wall_thickness);
	walls[2] = createBox(world, - wall_thickness, stage[3] / 2, wall_thickness, stage[3]);
	walls[3] = createBox(world, stage[2] + wall_thickness, stage[3] / 2, wall_thickness, stage[3]);
	walls[4] = createBox(world, stage[2] / 2, 0, $header.width() / 2, $header.height());

	wallsSetted = true;
}

function changeGravity(x, y){
	gravity = {x:x, y:y};
	setWalls();
}

// BROWSER DIMENSIONS

function getBrowserDimensions() {
	var changed = false;

	if (stage[0] != window.screenX) {
//		delta[0] = (window.screenX - stage[0]) * 50;
		stage[0] = window.screenX;
		changed = true;
	}
	if (stage[1] != window.screenY) {
//		delta[1] = (window.screenY - stage[1]) * 50;
		stage[1] = window.screenY;
		changed = true;
	}
	if (stage[2] != window.innerWidth) {
		stage[2] = window.innerWidth;
		changed = true;
	}
	if (stage[3] != window.innerHeight - 60) {
		stage[3] = window.innerHeight - 60;
		changed = true;
	}

	return changed;
}