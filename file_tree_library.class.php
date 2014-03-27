<?php

class file_tree_library
{
    var $odd = true;
    var $elementArray = array();
    var $nameOfStorage = "expandedStorage"; // Name of the array where the expanded nodes are stored.
//    var $downloadButton = "<input class=\"btn\" type=\"Submit\" name=\"download_files[]\" value=\"Download\" id=\"btn-act-deact\" style=\"float:right;margin-right:18px;font-size:13px;\">";
//                        echo "<input type=\"checkbox\" name=\"delete_files[]\" value=\"".$this->elementArray[$parentID][$no]['code']."\" style=\"float:right;margin-right:15px\">";
      
//            echo form_submit(array(
//                        'id'    => 'btn-act-deact',
//                        'name'  => 'action',
//                        'type'  => 'Submit',
//                        'class' => 'btn btn-primary pull-right',
//                        'value' => 'Download',
//                        'style' => 'margin-right:20px'
//            ));            <input type=\"checkbox\" name=\"download_files[]\" value=\"" . $row['id']."\"></td>";                           
//                    'id' => 'btn-act-deact',
//                    'name' => 'activate',
//                    'type' => 'Submit',
//                    'class' => 'btn btn-primary',
//                    'value' => 'Go'
//    var $feedbackButton = "<input class=\"action\" type=\"Submit\" name=\"feedback\" value=\"Feedback\" id=\"feedback\" style=\"float:right;margin-right:8px;font-size:13px;\" onclick=\"feedback()\">";
//    var $deleteCheckboox = "<input type=\"checkbox\" name=\"delete_files[]\" value=\"".$     ." style=\"float:left;margin-left: 15px;\">";
//                            echo "<td><input type=\"checkbox\" name=\"delete_milestones[]\" value=\"" . $row['milestone_id']."\"></td>"; 
    var $date = "";

    function writeCSS()
    {
        ?>
        <style type="text/css">

        #topNodes{
                margin-left:10px;
                padding-left:0px;
        }
        #topNodes ul{
                margin-left:20px;
                padding-left:15px;
               
                display:none;
        }
        #tree li{
                list-style-type:none;
                font-family:sans-serif;
                font-size:16px;
                padding-top:8px;
                border:1px solid #A0A0A0;
/*                border-bottom:none;*/
        }        
        #tree .tree_link{
                line-height:20px;
                padding-left:15px;
                padding-top:3px;
                padding-bottom:3px;
        }
        #tree img{
                padding-top:6px;
                 /*padding-bottom:3px;*/
        }
        #tree a{
                color: #282828;
                text-decoration:none;    
                
        }
        .activeNodeLink{
                line-height:18px;
                background-color: #316AC5;
                color: #FFFFFF;
                font-weight:bold;
                padding-top:6px;
                /*padding-left:15px;*/
        }
        </style>
        <?php
    }
    
    function writeJavascript()
    {
        ?>
        <script type="text/javascript">

        var plusNode = 'http://localhost/senior-projects//img/plus_folder.png';
        var minusNode = 'http://localhost/senior-projects//img/minus_folder.png';

        var nameOfStorage = '<?php echo $this->nameOfStorage; ?>';
        <?php
        $cookieValue = "";
        if(isset($_COOKIE[$this->nameOfStorage]))
        {
            $cookieValue = $_COOKIE[$this->nameOfStorage];
        }
        echo "var initExpandedNodes =\"".$cookieValue."\";\n";
        ?>
        function expandAll()
        {
            var treeObj = document.getElementById('tree');
            var images = treeObj.getElementsByTagName('IMG');
            for(var no=0;no<images.length;no++)
            {
                if(images[no].className=='tree_plusminus' && images[no].src.indexOf(plusNode)>=0)
                {
                    expandNode(false,images[no]);
                }
            }
        }
        
        function collapseAll()
        {
            var treeObj = document.getElementById('tree');
            var images = treeObj.getElementsByTagName('IMG');
            for(var no=0;no<images.length;no++)
            {
                if(images[no].className=='tree_plusminus' && images[no].src.indexOf(minusNode)>=0)
                {
                    expandNode(false,images[no]);
                }
            }
        }

        function expandNode(e,inputNode)
        {
            if(initExpandedNodes.length==0)
            {
                initExpandedNodes=",";
            }
            if(!inputNode)
            {
                inputNode = this;
            }
            if(inputNode.tagName.toLowerCase()!='img')
            {
                inputNode = inputNode.parentNode.getElementsByTagName('IMG')[0];
            }
            var inputId = inputNode.id.replace(/[^\d]/g,'');
            var parentUl = inputNode.parentNode;
            var subUl = parentUl.getElementsByTagName('UL');
            if(subUl.length==0)
            {
                return;
            }
            if(subUl[0].style.display=='' || subUl[0].style.display=='none')
            {
                subUl[0].style.display = 'block';
                inputNode.src = minusNode;
                initExpandedNodes = initExpandedNodes.replace(',' + inputId+',',',');
                initExpandedNodes = initExpandedNodes + inputId + ',';
            }
            else
            {
                subUl[0].style.display = '';
                inputNode.src = plusNode;
                initExpandedNodes = initExpandedNodes.replace(','+inputId+',',',');
            }
        }

        function initTree()
        {
            // Assigning mouse events
            var parentNode = document.getElementById('tree');
            var lis = parentNode.getElementsByTagName('LI'); // Get reference to all the images in the tree
            for(var no=0;no<lis.length;no++)
            {
                var subNodes = lis[no].getElementsByTagName('UL');
//                var subNodes = lis[no].getElementsByTagName('table');
                if(subNodes.length>0)
                {
                    lis[no].childNodes[0].style.visibility='visible';
                }
                else
                {
                    lis[no].childNodes[0].style.display='none';
                }
            }

            var images = parentNode.getElementsByTagName('IMG');
            for(var no=0;no<images.length;no++)
            {
                if(images[no].className=='tree_plusminus')
                {
                    images[no].onclick = expandNode;
                }
//                else
//                {
////                    if tree_plusminus is display none
//                }
            }

            var aTags = parentNode.getElementsByTagName('A');
            var cursor = 'pointer';
            if(document.all)
            {
                cursor = 'hand';
            }
            for(var no=0;no<aTags.length;no++)
            {
                aTags[no].onclick = expandNode;
                aTags[no].style.cursor = cursor;
            }
            var initExpandedArray = initExpandedNodes.split(',');

            for(var no=0;no<initExpandedArray.length;no++)
            {
                if(document.getElementById('plusMinus' + initExpandedArray[no]))
                {
                    var obj = document.getElementById('plusMinus' + initExpandedArray[no]);
                    expandNode(false,obj);
                }
            }
        }

        window.onload = initTree;

        </script>
        <?php
    }
    
    function addToArrayAss($element)
    {
        if(!isset($element['parentId']) || !$element['parentId'])
        {
            $element['parentId'] = 0;
        }
        $element['code'] = isset($element['code']) ? $element['code'] : 'javascript:return false';
        $element['url'] = isset($element['url']) ? $element['url'] : 'javascript:return false';
        $element['target'] = isset($element['target']) ? $element['target'] : '';
        $element['icon'] = isset($element['icon']) ? 'http://localhost/senior-projects//img/empty_folder2.png': '';
        $element['onclick'] = isset($element['onclick']) ? $element['onclick'] : '';
        $element['category'] = isset($element['category']) ? $element['category'] : 'uncateg';
        $element['date'] = isset($element['date']) ? $element['date'] : '';

        $this->elementArray[$element['parentId']][] = array(
                'id' => $element['id'],
                'code' => $element['code'],
                'title' => $element['title'],
                'url' => $element['url'],
                'target' => $element['target'],
                'icon' => $element['icon'],
                'onclick' => $element['onclick'],
                'category' => $element['category'],
                'date' => $element['date']
        );
    }

    function drawSubNode($parentID)
    {
        if(isset($this->elementArray[$parentID]))
        {            
            echo "<ul>";            
            for($no=0;$no<count($this->elementArray[$parentID]);$no++)
            {
                $urlAdd = " href=\"#\"";
                if($this->elementArray[$parentID][$no]['url'])
                {
                    $urlAdd = " href=\"".$this->elementArray[$parentID][$no]['url']."\"";
                    if($this->elementArray[$parentID][$no]['target'])
                    {
                        $urlAdd.=" target=\"".$this->elementArray[$parentID][$no]['target']."\"";
                    }
                }
                $onclick = "";
                if($this->elementArray[$parentID][$no]['onclick'])
                {
                    $onclick = " onmouseup=\"".$this->elementArray[$parentID][$no]['onclick'].";return false\"";
                }
                if($this->elementArray[$parentID][$no]['category'] == 'file')
                {
                    if($this->odd)
                    {
                        echo "<li class=\"tree_node\" style=\"padding-left:5px;height:30px;background:#E8E8E8;border-right:none;border-bottom:none;\">";
                        $this->odd = false;
                    }
                    else
                    {
                        echo "<li class=\"tree_node\" style=\"padding-left:5px;height:30px;background:#F0F0F0;border-right:none;border-bottom:none;\">";
                        $this->odd = true;
                    }
                    
                    echo "<img class=\"tree_plusminus\" id=\"plusMinus".$this->elementArray[$parentID][$no]['id']."\" src=\"http://localhost/senior-projects//img/plus_folder.png\">"
                            ."<img src=\"".$this->elementArray[$parentID][$no]['icon']."\">";

//                    echo $this->deleteCheckboox; 
//                     echo "<td><input type=\"checkbox\" name=\"delete_files[]\" value=\"" . $row['id']."\"></td>";
//                    echo "<input type=\"checkbox\" name=\"delete_files[]\" value=\"".$this->elementArray[$parentID][$no]['code']."\" style=\"float:left;margin-left: 15px;\">";
                    echo "<a class=\"tree_link\"$urlAdd$onclick>".$this->elementArray[$parentID][$no]['title']."</a>";            
                    
                    echo "<input type=\"checkbox\" name=\"delete_files[]\" value=\"".$this->elementArray[$parentID][$no]['code']."\" style=\"float:right;margin-right:15px\">";
//                                          'id'    => 'btn-act-deact',
//                        'name'  => 'action',
//                        'type'  => 'Submit',
//                        'class' => 'btn btn-primary pull-right',
//                        'value' => 'Delete',
//                        'style' => 'margin-right:20px'
//                                                  
//                  echo "<input type=\"checkbox\" name=\"delete_files[]\" value=\"".$this->elementArray[$parentID][$no]['code']."\" style=\"float:left;margin-left: 15px;\">";
                    echo "<input name=\"action\" class=\"btn-small btn-info\" type=\"Submit\" id=\"".$this->elementArray[$parentID][$no]['code']."\" value=\"Download\" style=\"float:right;margin-right:18px;font-size:13px;\">";
//                    echo $this->downloadButton;
                    $this->drawSubNode($this->elementArray[$parentID][$no]['id']);

                    echo "</li>";                    
                }
                elseif($this->elementArray[$parentID][$no]['category'] == 'milestone')
                {
                    echo "<li class=\"tree_node\" style=\"background:#F8F8F8;border-right:none;border-bottom:none;\">";
                        echo "<img class=\"tree_plusminus\" id=\"plusMinus".$this->elementArray[$parentID][$no]['id']."\" src=\"http://localhost/senior-projects//img/plus_folder.png\">"
                                ."<img src=\"".$this->elementArray[$parentID][$no]['icon']."\">";
                        echo "<b><a class=\"tree_link\"$urlAdd$onclick>".$this->elementArray[$parentID][$no]['title']."</a></b>";
                        $this->drawSubNode($this->elementArray[$parentID][$no]['id']);
                    echo "</li>"; 
                }
                else
                {
                    echo "<li class=\"tree_node\" style=\"background:#E8E8E8;border-right:none;border-bottom:none;\">";
                        echo "<img class=\"tree_plusminus\" id=\"plusMinus".$this->elementArray[$parentID][$no]['id']."\" src=\"http://localhost/senior-projects//img/plus_folder.png\">"
                                ."<img src=\"".$this->elementArray[$parentID][$no]['icon']."\">";
                        echo "<b><a class=\"tree_link\"$urlAdd$onclick>".$this->elementArray[$parentID][$no]['title']."</a></b>";
                        $this->drawSubNode($this->elementArray[$parentID][$no]['id']);
                    echo "</li>"; 
                }
            }
            echo "</ul>";            
        }                
    }

    function drawTree()
    {
        echo "<br>";
        echo "<div id=\"tree\">";
//         style=\"border:2px solid #a1a1a1;padding:10px 1px;
        
        echo "<ul id=\"topNodes\">";    
//        echo "<br>";
        echo form_submit(array(
                    'id'    => 'btn-act-deact',
                    'name'  => 'action',
                    'type'  => 'Submit',
                    'class' => 'btn btn-primary pull-right',
                    'value' => 'Delete',
                    'style' => 'margin-right:10px;margin-top:8px;',
                    
        ));   
        
        echo form_submit(array(
                    'id'    => 'expand',
                    'name'  => 'expand',
                    'type'  => 'Submit',
                    'class' => 'btn btn-primary pull-right',
                    'value' => 'Expand All',
                    'style' => 'margin-right:20px;margin-top:8px;',
                    'onclick' =>'expandAll();return false'
        ));
        echo form_submit(array(
                    'id'    => 'collapse',
                    'name'  => 'collapse',
                    'type'  => 'Submit',
                    'class' => 'btn btn-primary pull-right',
                    'value' => 'Collapse All',
                    'style' => 'margin-right:20px;margin-top:8px;',
                    'onclick' => 'collapseAll();return false'
        ));
//echo '<br>';
        for($no=0;$no<count($this->elementArray[0]);$no++)
        {
            $urlAdd = "";
            if($this->elementArray[0][$no]['url'])
            {
                $urlAdd = " href=\"".$this->elementArray[0][$no]['url']."\"";
                if($this->elementArray[0][$no]['target'])
                {
                    $urlAdd.=" target=\"".$this->elementArray[0][$no]['target']."\"";
                }
            }
            $onclick = "";
            if($this->elementArray[0][$no]['onclick'])
            {
                $onclick = " onmouseup=\"".$this->elementArray[0][$no]['onclick'].";return false\"";
            }
            
            echo "<li class=\"tree_node\" id=\"node_".$this->elementArray[0][$no]['id']."\">";
            
            echo "<img id=\"plusMinus".$this->elementArray[0][$no]['id']."\" class=\"tree_plusminus\" src=\"http://localhost/senior-projects//img/plus_folder.png\">";
            echo "<img src=\"".$this->elementArray[0][$no]['icon']."\">";
            echo "<b><a class=\"tree_link\"$urlAdd$onclick>".$this->elementArray[0][$no]['title']."</a></b>";
//            echo "<div>";
//            echo "<hr>";
            $this->drawSubNode($this->elementArray[0][$no]['id']);
//            echo "<hr>";
//            echo "</div>";
            echo "</li>";            
        }
        echo "</ul>";        
        echo "</div>";
    }
    
    function isEmpty()
    {
        $answer= true;
        if(parentId > 0)
        {
            $answer = false;
        }
        return $answer;
    }
}
?>