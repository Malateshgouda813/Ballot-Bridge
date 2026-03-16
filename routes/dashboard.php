<?php
session_start();
include("../api/connect.php");

// Redirect if not logged in
if(!isset($_SESSION['userdata'])){
    header("Location: ../index.html");
    exit();
}

$leader_query = mysqli_query($connect,"SELECT name, votes FROM user WHERE role=2 ORDER BY votes DESC LIMIT 1");
$leader = mysqli_fetch_assoc($leader_query);

$user = $_SESSION['userdata'];
$groupsdata = $_SESSION['groupsdata'] ?? [];

$groups = mysqli_query($connect,"SELECT name, votes FROM user WHERE role=2");

$group_names = [];
$group_votes = [];

while($row = mysqli_fetch_assoc($groups)){
    $group_names[] = $row['name'];
    $group_votes[] = $row['votes'];
}
?>

<html>
<head>
<title>Dashboard</title>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<link rel="stylesheet" href="../css/stylesheet.css">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>

#leaderSection{
    background:#c2c5c6;
    margin-top:20px;
    padding:20px;
    border-radius:10px;
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.downloadBox a{
    font-size:24px;
    text-decoration:none;
}

.downloadBox a:hover{
    transform:scale(1.2);
}    

body{
    font-family: Arial;
    background:#f1f2f6;
}

#headerSection{
    margin-bottom:20px;
}

#backbtn,#logoutbtn{
    padding:6px 12px;
    border-radius:6px;
    background:#29c0e1;
    color:white;
    border:none;
    cursor:pointer;
}

#backbtn{ float:left; }
#logoutbtn{ float:right; }

#mainSection{
    max-width:1100px;
    margin:auto;
    padding:20px;
}

.topSection{
    display:flex;
    gap:20px;
    margin-bottom:20px;
}

.card{
    background:#c2c5c6;
    padding:20px;
    border-radius:8px;
    border:1px solid #999;
}

/* PROFILE */
#Profile{
    flex:1;
    padding:20px;
    border-radius:10px;
}

/* GRAPH */
#graph{
    flex:1;
    padding:20px;
    border-radius:10px;
}

/* GROUP SECTION */
#Group{
    width:96%;
    padding:20px;
    border-radius:10px;
}

.groupCard{
    background:white;
    border:1px solid #ccc;
    border-radius:8px;
    padding:15px;
    margin-bottom:15px;
    display:flex;
    align-items:center;
    gap:15px;
    max-width:900px;
}

.groupCard img{
    border-radius:6px;
}

#votebtn{
    padding:6px 12px;
    border:none;
    border-radius:5px;
    background:#48dbfb;
    color:white;
    cursor:pointer;
}

@media screen and (max-width:768px){

.topSection{
    flex-direction:column;
}

.groupCard{
    flex-direction:column;
    text-align:center;
}

.groupCard img{
    margin-bottom:10px;
}

#backbtn,#logoutbtn{
    float:none;
    margin:5px;
}

#votebtn{
    padding:8px 16px;
    font-size:14px;
}

}

</style>

</head>





<body>

<div id="mainSection">

<div id="headerSection">

<button id="backbtn" onclick="window.history.back()">Back</button>
<button id="logoutbtn" onclick="window.location='../routes/logout.php'">Logout</button>

<center>

<h1>Online Voting System</h1>

<p id="quote" style="font-style:italic; color:#444; margin-top:10px;"></p>

<h3>Welcome, <?php echo htmlspecialchars($user['name']); ?></h3>

<p>Your role: <?php echo $user['role']==1?'Voter':'Group'; ?></p>

<p>Status: <?php echo $user['status']==0?'Not Voted':'Voted'; ?></p>

</center>

</div>

<hr>

<!-- TOP SECTION -->
<div class="topSection">

<!-- PROFILE -->
<div id="Profile" class="card">

<h2>Your Profile</h2>

<?php
$photo = $user['photo'];
if(empty($photo)){
    $photo="male.png";
}
?>

<img src="../uploads/<?php echo $photo;?>" height="120" width="120"><br><br>

<p><b>Name:</b> <?php echo htmlspecialchars($user['name']); ?></p>
<p><b>Role:</b> <?php echo $user['role']==1?'Voter':'Group'; ?></p>
<p><b>Mobile:</b> <?php echo htmlspecialchars($user['mobile']); ?></p>
<p><b>Address:</b> <?php echo htmlspecialchars($user['address']); ?></p>

</div>


<!-- GRAPH -->
<div id="graph" class="card">

<h2>Voting Results</h2>

<canvas id="voteChart"></canvas>

</div>

</div>


<!-- GROUP SECTION -->

<div id="Group" class="card">

<center><h2>Groups</h2></center>

<?php if(!empty($groupsdata['groups'])): ?>

<?php foreach($groupsdata['groups'] as $group): ?>

<div class="groupCard">

<img src="../uploads/<?php echo htmlspecialchars($group['photo']); ?>" height="80" width="80">

<div>

<b>Group Name:</b> <?php echo htmlspecialchars($group['name']); ?><br>

<b>Votes:</b> <?php echo $group['votes']; ?><br>

<?php if($user['status']==0): ?>

<form action="../api/vote.php" method="POST">

<input type="hidden" name="gid" value="<?php echo $group['id']; ?>">
<input type="hidden" name="gvotes" value="<?php echo $group['votes']; ?>">

<input type="submit" value="Vote" id="votebtn">

</form>

<?php else: ?>

<p><i>You already voted</i></p>

<?php endif; ?>

</div>

</div>

<?php endforeach; ?>

<?php else: ?>

<p>No groups available.</p>

<?php endif; ?>

</div>


<!-- LEADER SECTION -->

<div id="leaderSection">

<h3>🏆 Present Leading : </h3>

<?php if($leader){ ?>

<p>
<b><?php echo $leader['name']; ?></b> is currently leading with 
<b><?php echo $leader['votes']; ?></b> votes.
</p>

<?php } else { ?>

<p>No votes yet.</p>

<?php } ?>

<div class="downloadBox">
<a href="../api/download_result.php" title="Download Results">⬇️ Download Results</a>
</div>

</div>

</div>


<!-- CHART SCRIPT -->

<script>

const ctx = document.getElementById('voteChart');

new Chart(ctx, {

type:'bar',

data:{
labels: <?php echo json_encode($group_names); ?>,

datasets:[{

label:'Votes',

data: <?php echo json_encode($group_votes); ?>,

backgroundColor:[
'#ff6b6b',
'#48dbfb',
'#1dd1a1',
'#feca57',
'#5f27cd',
'#ee5253',
'#54a0ff'
],

borderColor:'#333',
borderWidth:1

}]
},

options:{
responsive:true,
plugins:{
legend:{
display:false
}
},
scales:{
y:{
beginAtZero:true
}
}
}

});

</script>


<script>

const quotes = [

"Every citizen above 18 has the right to vote and shape the future of the nation.",

"Your vote is your voice, make it count.",

"A strong democracy depends on active participation from its citizens.",

"If you are 18 or older, your decision can help build a better tomorrow.",

"Choose your leader wisely and participate in the democratic process.",

"Every single vote has the power to bring change.",

"Do not stay silent, express your choice through voting.",

"Together, our votes create a stronger and fairer society.",

"Vote today to create a better future for everyone."

];

const randomQuote = quotes[Math.floor(Math.random() * quotes.length)];

document.getElementById("quote").innerText = randomQuote;

</script>



</body>

</html>