<?php 
//   Uploading File
     $filename = explode('.', $_FILES["image"]["name"]);
     $date = new DateTime();
     $returnurl = 'Photos/';
     $returnurl .= $date->format("YmdHis");
     $returnurl .= '.'.$filename[1];
      move_uploaded_file($_FILES["image"]["tmp_name"],$returnurl);
 //  Save the record in the data base
      $Name = $_POST["Name"];
      $Fbid = $_POST["Fbid"];
      $Url = $_POST["Url"];
      $Email = $_POST["Email"];
      $con = mysqli_connect("myapp.com", "root", "", "facebook");

      if(mysqli_connect_errno())
        return "failed";
//      //Insert the User
      $query = "select * from users where Fbid='".$Fbid ."'";
      $res = mysqli_query($con, $query);
      if($res->num_rows!= 0)
      {
        mysqli_query($con,"Insert into Image (Path,Fbid) Values ('".$returnurl."','".$Fbid."')");
      }
      else {
         $flag_insert =  mysql_query("Insert into users (Name,Fbid,Email,Url) Values ('". $Name ."','". $Fbid."','". $Email."','". $Url."')");
        if($flag_insert) // if the record inserted
        {
          mysqli_query($con,"Insert into Image (Path,Fbid) Values ('".$returnurl."','".$Fbid."')");
        }
      }
      mysqli_close($con);
      return $returnurl;
     
?>