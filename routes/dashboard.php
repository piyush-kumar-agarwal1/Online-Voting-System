<?php
    session_start();
    if(!isset($_SESSION['userdata'])){
        header("location: ../");
    }

    $userdata = $_SESSION['userdata'];
    $groupsdata = $_SESSION['groupsdata'];

    if($_SESSION['userdata']['status']==0){
        $status = '<b style="color:red"> Not Voted</b>';
    }
    else{
        $status = '<b style="color:green">Voted</b>';
    }
?>

<html>
    <head>
        <title>Online Voting System - Dashboard</title>
        <link rel="stylesheet" href="../css/stylesheet.css">
    </head>
    <body> 

    <style>
    #backbtn {
        padding: 10px 20px;
        font-size: 1em;
        border-radius: 5px;
        background-color: #3498db;
        color: white;
        float: left;
        margin: 10px;
        border: none;
        cursor: pointer;
        transition: background-color 0.3s ease, transform 0.3s ease;
    }
    
    #backbtn:hover {
        background-color: #2980b9;
        transform: scale(1.05);
    }
    
    #logoutbtn {
        padding: 10px 20px;
        font-size: 1em;
        border-radius: 5px;
        background-color: #3498db;
        color: white;
        float: right;
        margin: 10px;
        border: none;
        cursor: pointer;
        transition: background-color 0.3s ease, transform 0.3s ease;
    }
    
    #logoutbtn:hover {
        background-color: #2980b9;
        transform: scale(1.05);
    }
    
    #Profile {
        background-color: white;
        width: 30%;
        padding: 20px;
        float: left;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    }
    
    #Group {
        background-color: white;
        width: 60%;
        padding: 20px;
        float: right;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    }
    
    #votebtn {
        padding: 10px 20px;
        font-size: 1em;
        border-radius: 5px;
        background-color: #3498db;
        color: white;
        border: none;
        cursor: pointer;
        transition: background-color 0.3s ease, transform 0.3s ease;
    }
    
    #votebtn:hover {
        background-color: #2980b9;
        transform: scale(1.05);
    }
    
    #mainpanel {
        padding: 10px;
    }
    
    #headerSection {
        padding: 20px;
        background-color: #2c3e50;
        color: white;
        text-align: center;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    #voted {
        padding: 10px 20px;
        font-size: 1em;
        border-radius: 5px;
        background-color: green;
        color: white;
        border: none;
        cursor: pointer;
        transition: background-color 0.3s ease, transform 0.3s ease;
    }
    
    #voted:hover {
        background-color: #27ae60;
        transform: scale(1.05);
    }

    </style>

    <!-- <button>Contact Me</button><br><br>
    <a href="contact.html">Click here</a> -->


    <div id="mainSection">
        <center>
    <div id="headerSection">
    <a href="../"><button id="backbtn">  Back</button></a>
    <a href="logout.php"><button id="logoutbtn">  Logout</button></a>
    <h1>Online Voting System</h1>
    </div>
    </center>
    <hr>

    <div id="mainpanel">
    <div id="Profile">
        <center><img src="../uploads/<?php echo $userdata['photo']?>" height="100" width="100"></center><br><br>
        <b>Name:</b> <?php echo $userdata['name']?><br><br>
        <b>Mobile:</b> <?php echo $userdata['mobile']?><br><br>
        <b>Address:</b> <?php echo $userdata['address']?><br><br>
        <b>Status:</b> <?php echo $status?><br><br>
    </div>

    <div id="Group">
        <?php
        if($_SESSION['groupsdata']){
            for($i=0; $i<count($groupsdata); $i++){
                ?>
                <div>
                    <img style="float: right" src="../uploads/<?php echo $groupsdata[$i]['photo'] ?>" height="100" width:="100">
                    <b>Group Name: </b><?php echo $groupsdata[$i]['name']?><br><br>
                    <b>Votes: </b><?php echo $groupsdata[$i]['votes']?><br><br>
                    <form action="../api/vote.php" method="POST">
                        <input type="hidden" name="gvotes" value="<?php echo $groupsdata[$i]['votes']?>">
                        <input type="hidden" name="gid" value="<?php echo $groupsdata[$i]['id']?>">
                        <?php
                            if($_SESSION['userdata']['status']==0){
                                ?>
                        <input type="submit" name="votebtn" value="Vote" id="votebtn">
                                <?php
                            }
                            else{
                                ?>
                                <button disabled type="button" name="votebtn" value="Vote" id="voted">Voted</button>
                                        <?php
                            }
                            ?>
                    </form>
                </div>
                <hr>
                <?php
            }
        }
        else{

        }

        ?>
    </div>
    </div>



    </div>

    </body>

</html>