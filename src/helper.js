export function getClientOS() {
    var os = null; 

    if(navigator.appVersion.indexOf("Win")!=-1) os="windows";
    if(navigator.appVersion.indexOf("Mac")!=-1) os="mac";
    if(navigator.appVersion.indexOf("X11")!=-1) os="unix";
    if(navigator.appVersion.indexOf("Linux")!=-1) os="linux";

    return os;
}


export function splitData(opt, searchKey) {
    
    console.log('data 1: '+JSON.stringify(opt))    

    var s = searchKey.toLowerCase();
    var arr = [];
    var arrNew = [];
    var displayName = '';
    var searchName = '';

    var level2 = "<span style='margin-left:20px'></span>";
    var level3 = "<span style='margin-left:40px'></span>";
    for (var i = 0; i < opt.length; i++) 
    {
        displayName = opt[i]['lev1_name'];
        searchName = opt[i]['lev1_name'];
        arr.push(opt[i]['lev1_name']+"~|~"+opt[i]['lev1_CID']+"~|~"+searchName+"~|~"+opt[i]['lev1']+"~|~"+displayName);

        if(opt[i]['lev2_name'])
        {
            displayName = level2+opt[i]['lev2_name'];
            searchName = opt[i]['lev1_name']+" <span style='font-size:14px;'>&#9656;</span> "+opt[i]['lev2_name'];
            arr.push(opt[i]['lev2_name']+"~|~"+opt[i]['lev2_CID']+"~|~"+searchName+"~|~"+opt[i]['lev2']+"~|~"+displayName);
            
            if(opt[i]['lev3_name'])
            {
                displayName = level3+opt[i]['lev3_name'];
                searchName = opt[i]['lev1_name']+" <span style='font-size:14px;'>&#9656;</span> "+opt[i]['lev2_name']+" <span style='font-size:14px;'>&#9656;</span> "+opt[i]['lev3_name'];
                arr.push(opt[i]['lev3_name']+"~|~"+opt[i]['lev3_CID']+"~|~"+searchName+"~|~"+opt[i]['lev3']+"~|~"+displayName);
            }
        }
    }

    let unique = [...new Set(arr)];

    for (var i = 0; i < unique.length; i++) {
        var fields = unique[i].split('~|~');
        arrNew.push([fields[0],fields[1],fields[2],fields[3],fields[4]]);   
    }
        
    return arrNew;
    //return arrNew.filter(c => c[2].toLowerCase().indexOf(s) > -1);
}

export function handleDup(d, opt) {
    
    // var arr = [];
    // var arrNew = [];

    // if(d == "Cat")
    // {
    //     for (var i = 0; i < opt.length; i++) 
    //         arr.push(opt[i]['catName']+"~|~"+opt[i]['catID']+"~|~"+opt[i]['completeName']+"~|~"+opt[i]['level']);
        
    //     let unique = [...new Set(arr)];

    //     for (var i = 0; i < unique.length; i++) {
    //         var fields = unique[i].split('~|~');
    //         arrNew.push({'catName':fields[0],'catID':fields[1],'completeName':fields[2], 'level':fields[3]});   
    //     }
    // }
    // else if(d == "Prod")
    // {
    //     for (var i = 0; i < opt.length; i++) 
    //         arr.push(opt[i]['prodName']+"~|~"+opt[i]['prodID']);
        
    //     let unique = [...new Set(arr)];

    //     for (var i = 0; i < unique.length; i++) {
    //         var fields = unique[i].split('~|~');
    //         arrNew.push({'prodName':fields[0],'prodID':fields[1]});   
    //     }
    // }
    // return arrNew;
}



export function getSavedCamCategory(d, catByLevel) {
    
    var arr = [];
    var arrNew = [];

    for (var i = 0; i < d.length; i++) 
    {

        if(d[i]['level'] == "1")
        {
            arr = catByLevel.filter(c => c.lev1_CID === d[i]['catID']);
            arrNew.push({'catName':arr[0]['lev1_name'],'catID':d[i]['catID'],'completeName':arr[0]['lev1_name'], 'level':d[i]['level'], 'camCatID':d[i]['camCatID']});      
        }
        else if(d[i]['level'] == "2")
        {
            arr = catByLevel.filter(c => c.lev2_CID === d[i]['catID']);
            arrNew.push({'catName':arr[0]['lev1_name'],'catID':d[i]['catID'],'completeName':arr[0]['lev1_name']+" <span style='font-size:14px;'>&#9656;</span> "+arr[0]['lev2_name'], 'level':d[i]['level'], 'camCatID':d[i]['camCatID']});    
        }
        else if(d[i]['level'] == "3")
        {
            arr = catByLevel.filter(c => c.lev3_CID === d[i]['catID']);
            arrNew.push({'catName':arr[0]['lev1_name'],'catID':d[i]['catID'],'completeName':arr[0]['lev1_name']+" <span style='font-size:14px;'>&#9656;</span> "+arr[0]['lev2_name']+" <span style='font-size:14px;'>&#9656;</span> "+arr[0]['lev3_name'], 'level':d[i]['level'], 'camCatID':d[i]['camCatID']});    
        }
    }
    return arrNew;
}

export function getSavedCamProducts(d) {
       
    var arrNew = [];
    for (var i = 0; i < d.length; i++) 
        arrNew.push({'prodName':d[i]['ProdName'], 'prodID':d[i]['prodID'], 'camProdID':d[i]['camProdID']});     

    return arrNew;
}

export function getSavedCamCountries(d) {
       
    var arrNew = [];
    for (var i = 0; i < d.length; i++) 
        arrNew.push({'camCounID':d[i]['camCounID'], 'name':d[i]['name']});     

    return arrNew;
}


export function getSavedCamDiscounts(d) {
    
    var arrNew = [];
    for (var i = 0; i < d.length; i++) 
        arrNew.push({'camDiscID':d[i]['camDiscID'], 'discountCode':d[i]['discountCode'], 'description': d[i]['description'],'numberOfUse': d[i]['numberOfUse']});     
    return arrNew;
}


export function setDiscountsOption(d) {
       
    var arrNew = [];
    var dType = '';
    for (var i = 0; i < d.length; i++) 
    {   
        dType = d[i]['discountAmount']+d[i]['discountType'];
        if(d[i]['discountType'] == "$")
            dType = d[i]['discountType']+d[i]['discountAmount'];
        if(d[i]['discountCode'].length>0)
            arrNew.push({'value':d[i]['discountCode'], 'text':d[i]['discountCode']+" "+dType});     
        else
            arrNew.push({'value':d[i]['discountCode'], 'text':d[i]['discountCode']});     
    }
    //arrNew.push({'value':'', 'text':'Remove Discount'});  
    return arrNew;
}
