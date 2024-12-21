<style>
	.tbl_1      {                                        border-color: #787878; border-width: .5pt .5pt .5pt .5pt; border-style: solid; border-collapse:collapse; }
	.td_1       { font-family: Verdana; font-size: 12px; border-color: #787878; border-width: .5pt .5pt .5pt .5pt; border-style: solid; }
	.td_1_bold  { font-family: Verdana; font-size: 9px; border-color: #787878; border-width: .5pt .5pt .5pt .5pt; border-style: solid; font-weight: bold;  }
	.td_2       { font-family: Verdana; font-size: 12px;  }

	.p1         { font-family: Verdana; font-size: 14px;      }

	.p3			{ font-family: Verdana; font-size: 12px;      }
	.p3_red		{ font-family: Verdana; font-size: 12px;  color: red;   font-weight: bold; text-align: center;}
	.p3_green	{ font-family: Verdana; font-size: 12px;  color: green; font-weight: bold; text-align: center;}

	.p4         { font-family: Verdana; font-size: 10px;      }
	.a1         { font-family: Verdana; font-size: 12px;  color: blue;  text-decoration: none;   }
	.a3         { font-family: Verdana; font-size: 20px;  color: blue;  text-decoration: none;   }
	.a3_underl  { font-family: Verdana; font-size: 20px;  color: blue;  text-decoration: underline;   }
        
    .box_user{
        position: fixed; 
        top: 0; 
        right: 0; 
        text-align: right; 
        padding: 10px 10px 10px 10px; 
        margin: 10px 10px 10px 10px;
        background-color: white;
        border: 2px solid darkgray;
        border-radius: 5px;
    }
    .box_error{
        border: 1px solid;
        margin: 10px 0px;
        padding: 10px;
        background-repeat: no-repeat;
        background-position: 10px center;
        color: #D8000C;
        background-color: #FFBABA;
        border-radius: 5px;
        font-family: Arial;
        width: 100%;
    }
    .box_info{
        border: 1px solid;
        margin: 10px 0px;
        padding: 10px;
        background-repeat: no-repeat;
        background-position: 10px center;
        color: #007600;
        background-color: #DBF9DB;
        border-radius: 5px;
        font-family: Arial;
        width: 100%;
    }

    .form-control {
            display: block;
            width: 100%;
            padding: 10px;
            font-size: 1rem;
            font-weight: 400;
            line-height: 1.5;
            color: #212529;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #ced4da;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            border-radius: 0.25rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    .btn-primary {
        margin: 10px;
        font-size: 20px;
        width: 80px;
        height: 40px;
        padding: 5px;
        background: #0055ff;
        color: #fff;
        outline: none;
        border-radius: 4px;
        border: 1px solid transparent;
        transition: 0.5s;
    }
    .btn-primary:hover {
        background: #0055dd;
        color: #fff;
        transition: 0.5s;
    }
    .btn-primary:focus {
        box-shadow: 0 0 0 5px rgba(0, 85, 255, 0.5)
    }
    

    .btn-primary2 {
        margin: 10px;
        font-size: 20px;
        width: 180px;
        height: 40px;
        padding: 5px;
        background: #0055ff;
        color: #fff;
        outline: none;
        border-radius: 4px;
        border: 1px solid transparent;
        transition: 0.5s;
    }
    .btn-primary2:hover {
        background: #0055dd;
        color: #fff;
        transition: 0.5s;
    }
    .btn-primary2:focus {
        box-shadow: 0 0 0 5px rgba(0, 85, 255, 0.5)
    }


    
    .parent {
        width: 100%;
        height: 100%;
        position: absolute;
        top: 0;
        left: 0;
        overflow: auto;
        /*background-color: red;*/
    }
    .block {
        width: 600px;
        height: 250px;
        position: absolute;
        top: 50%;
        left: 50%;
        margin: -200px 0 0 -300px;
        /*background-color: green;*/
    }
    
    .h1 {   font-size: 38px; font-family: Arial;}
    .h2 {   font-size: 20px; font-family: Arial;}
</style>
