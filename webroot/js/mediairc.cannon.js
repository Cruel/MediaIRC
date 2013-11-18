function drawOk(position){
	var canvas = document.getElementById('canvas');
    var context = canvas.getContext('2d');
    context.clearRect(0, 0, canvas.width, canvas.height);

    var radius = 70;

    context.beginPath();
    context.arc(position.x, position.y, radius, 0, 2 * Math.PI, false);

    context.save();
    context.fillStyle = 'red';
    context.shadowColor = '#999';
    context.shadowBlur = 20;
    context.shadowOffsetX = 6;
    context.shadowOffsetY = -6;
    context.fill();
    context.restore();
    
    context.lineWidth = 5;
    context.strokeStyle = '#003300';
    context.stroke();
}

$(function(){
	// Setup our world
	var world = new CANNON.World();
	world.gravity.set(0,-10,0);
	world.broadphase = new CANNON.NaiveBroadphase();

	// Create a sphere
	var mass = 5, radius = 70;
	var sphereShape = new CANNON.Sphere(radius);
	var sphereBody = new CANNON.RigidBody(mass,sphereShape);
	sphereBody.position.set(200,200,0);
	world.add(sphereBody);

	// Create a plane
	var groundShape = new CANNON.Plane();
	console.log(groundShape);
	var groundBody = new CANNON.RigidBody(0,new CANNON.Box(new CANNON.Vec3( 500, .01, 25 )));
	world.add(groundBody);

	// Step the simulation
	setInterval(function(){
	  world.step(1.0/40.0);
	  console.log(sphereBody.position);
	  drawOk(sphereBody.position);
	}, 1000.0/40.0);
});