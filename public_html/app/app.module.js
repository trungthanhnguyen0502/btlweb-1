var myApp = angular.module("myApp" , ['ui.router', 'ngRoute', 'naif.base64', 'ngSanitize'])

myApp.value('maps', {
    'priority'     :{ 1: 'thấp' , 2:'bình thường' , 3: 'cao' , 4: 'feedback' , 5:'khẩn cấp'},
    'ticket_status':{1: 'new' , 2:'inprogress' ,3:'resolved', 4:'feedback', 5:'closed' , 6:'cancelled' ,7:'out_of_date'},
    'rating'       :{0:'không hài lòng' , 1:'hài lòng'},
    'type'         :{0:'không', 1:'đánh giá' , 2:'thay đổi độ ưu tiên' , 3:'thay đổi deadline'},
    'ticket_read_status' :{0:'chưa đọc' , 1:'đã đọc'},
    'team'         :{0:'Hà Nội-IT' , 1:'Đà Nẵng-IT '}
})




myApp.value('EV_dictionary' , {
    'my_request': 'Việc tôi yêu cầu',
    'related_request': 'Công việc liên quan',
    'mission': 'Nhiệm vụ được giao',
    'team_request': 'Công việc của nhóm'
})

myApp.config(function($stateProvider, $urlRouterProvider) {
    
    $stateProvider
        .state('home', {
            url: '/home',
            templateUrl: './app/components/Home/home.html',
        })
    $stateProvider
        .state('dashBoard' , {
            url: '/dash_board/:name/:condition',
            templateUrl:'app/components/dashboard/dashboard.html',
            controller: 'dashBoardController',
        })


    $stateProvider
        .state('ticketDetail' , {
            url: '/ticket/:id',
            templateUrl:'app/components/ticketDetail/ticketDetail.html',
            controller: 'ticketDetailController'
        })

    $urlRouterProvider.otherwise('/dash_board/my_request/all')
})

myApp.run(['$rootScope' ,'$http',  function( $rootScope , $http){
    $rootScope.user = new User()
    $rootScope.user.id = 1
    $rootScope.user.team_id = 1
    $rootScope.user.userName = "trungthanhnguyen"
    // $http.get('/get_user').success( function(response){
    //         if( response.user && response.user.id )
    //             $rootScope.user = new User(user)
    //         else
    //             alert('dữ liệu sai')
    //         })
    //     .error( function( data){
    //         alert("dữ liệu sai")
    //     })
}])

myApp.directive('uploadFiles', function () {  
    return {  
        scope: true,        //create a new scope  
        link: function (scope, el, attrs) {  
            el.bind('change', function (event) {  
                var files = event.target.files  
               for (var i = 0 ;i < files.length; i++) {  
                    scope.$emit("seletedFile", { file: files[i] })  
                }  
            })  
        }  
    }  
}) 

myApp.component('userHeader' , {
    templateUrl: './app/common/userHeader.html',
    controller: 'userHeaderController'
})







myApp.component('sideBar',{
    templateUrl: './app/components/SideBar/sideBar.html',
    controller: 'sideBarController',
    bindings:{
        name: '=',
    },
})
myApp.component('newRequest', {
    templateUrl: './app/components/newRequest/newRequest.html',
    controller: 'newRequestController',
    bindings:{
        ticket: '=',
    }
})
myApp.component('myFooter',{
    templateUrl: './app/common/footer.html',
})






myApp.filter('underline', function(){
    return function(input){
        return input.replace(" " , "_")
    }
})
myApp.filter('toNomal' , function(){
    return function(input){
        input = input.charAt(0).toUpperCase() + input.slice(1)
        return input.replace("_" ," ")
    }
})

//filter
myApp.filter('toPriority', ['mapService' , function( mapService){
    return function(input){
        result = mapService.map(input , 'priority')
        return result
    }
}])
myApp.filter('toTicketStatus', ['mapService' , function( mapService){
    return function(input){
        return mapService.map(input , 'ticket_status')
    }
}])
myApp.filter('toRating', ['mapService' , function( mapService){
    return function(input){
        return mapService.map(input , 'rating')
    }
}])
myApp.filter('toTeam', ['mapService' , function( mapService){
    return function(input){
        return mapService.map(input , 'team')
    }
}])
myApp.filter('toTypeComment', ['mapService' , function( mapService){
    return function(input){
        return mapService.map(input , 'type')
    }
}])
myApp.filter('toReadStatus', ['mapService' , function( mapService){
    return function(input){
        return mapService.map(input , 'ticket_read_status')
    }
}])
myApp.filter('VietNamTrans' , ['EV_dictionary' , function(EV_dictionary){
    return function(input){
        return EV_dictionary[input]
    }
}])












myApp.service('mapService' , [ 'maps' , function(maps){
    this.map = function( input,map_name){
         return maps[map_name][input]
    }
}])


myApp.service('conditionFilterService', function(){
    this.filterCondition = function( condition){
        result = {}
        for (var pro in condition){
            if (condition[pro] )
                 result[pro] = condition[pro]
        }
        return result
    }
})

myApp.service('ticketService', ['conditionFilterService', '$http', function(conditionFilterService ,$http){
    
    this.getTicket = function(id){
        url = ""
        data = {}
        data.id = id
        $http.get(url , {params: data}).success( function(response){
            // ticket = response.data.ticket
            // return ticket
        }).error( function(){
            alert("fake data")
            ticket = new Ticket()
            ticket.id = id
            return ticket
        })
    }
    this.getTickets = function( condition){
        condition.status = parseInt(condition.status)
        condition.priority = parseInt(condition.priority)
        condition = conditionFilterService.filterCondition(condition)
        url = "/api/get-tickets"
        data = condition
        console.log(data)
        
        $http.get(url , {params: data}).success( function(response){
            ticket = response
            console.log(ticket)
            return ticket
        }).error( function(){
            alert("tìm kiếm thất bại")
        })  
    }

    this.deleteTickets = function(id){
        url = ""
        $http.delete(url , {id: id}).success(function(response) {
            console.log(response);
        }).error(function() {
            console.log("error");
        });
    }
    this.newTicket = function(data){
        if( angular.isArray(data)){
            Tickets = []
            for( var obj in data){
                Tickets.push( new Ticket(obj))
            }
            return Tickets
        }
        else{
            return new Ticket(data)
        }
    }

    this.saveTicket = function(ticket){
        data = {}
        for (var pro in ticket){
            if( ticket[pro])
                data[pro] = ticket[pro]
        }
        console.log(data)
        $http.post("/api/create-ticket" ,data).
            success(function (response) {
                alert("success!");
            }).
            error(function (response) {
                console.log(response)
            });
    }
    this.countTicket = function(condition){
        
    }
    this.checkTicket = function( ticket){
        if( ticket.status == -1  || ticket.priority == -1 || ticket.rating == -1 || ticket.team_id == -1 ){
            return false
        }
        return true
    }
    this.editTicket  = function( ticket){
        if( ! this.checkTicket(ticket)|| ticket.id == -1  || ! ticket.id )
            alert("data is wrong")
        else{
            // url = ""
            // data = ticket
            // $http.put(url , data).success(function (response) {
            //          alert("success!");
            //      }).
            //     error(function (response) {
            //          alert("failed!");
            //     });
        }
    }

    this.SearchSubject = function( subject){
        //fake 
        result = []
        for( var i=0; i<10;i++){
            result.push("công việc thứ " + i.toString())
        }
        return result

    }
  
    this.getRelatedUser = function(ticket_id){
        $http.get("" , {id:ticket_id}).success( function(response){
            return 
        }).error(function(response){
            alert("dữ liệu bị lỗi")
        })
    }
    this.fakeData = function(_id){
        data = {id: _id,
                subject:"this is subject "+ _id.toString(),
                content:"this is content",
                creat_by:1, status:1 ,priority:1 
        }
        return new Ticket(data)
    }
    this.fakeDataList = function(dataNumber){
        dataList = []
        for( i = 0 ;i < dataNumber; i++ )
            dataList.push( this.fakeData(i))
        return dataList
    }
}])

myApp.service('userService' ,['$http' , function(){
    this.getById = function(){

    }
    this.getSameName = function( name){
        // url 
        // $http.get( url )
    }


}])


myApp.service('fakeDataService', function(){  
    this.fakeTickets = function(first_id , number ){
        result = []
        for( i= first_id ; i< first_id + number; i++){
            t = new Ticket()
            t.id = i
            t.subject = "công việc thứ "+ i.toString()
            t.content = "nội dung công việc thứ " + i.toString()
            t.deadline = new Date()
            t.team_id = i % 2 == 0 ? 1: 0
            t.is_read = i % 2 == 0 ? 1: 0
            t.assigned_to = i
            t.status = Math.floor(Math.random() *5) + 1; 
            t.priority = Math.floor(Math.random() *4) + 1
            t.is_read = i%2 == 0 ? 1:0
            result.push(t)

        }
        return result;
    }
    this.fakeComments = function(first_id , ticket_id , number, content){
        result = []
        for( var i = first_id ; i < first_id + number ; i++){
            comment = new Comment()
            comment.id = i
            comment.ticket_id = ticket_id
            comment.create_at = new Date()
            comment.user_id = Math.floor(Math.random() * 5) + 1; 
            comment.content = content ? content : " this is content comment"
            comment.user_name = i % 2 ==0? " nguyễn Thành Trung " : "Trung Thành Nguyễn " 
            comment.created_at = new Date()
            result.push(comment)
        }
        return result
    }

    this.fakeUser = function(){
        result = []
        for( var i = 0 ; i < 10 ; i++){
            user = new User()
            user.id = i
            user.username   = "nguyễn Thành Trung " + i.toString()
            user.email      = "trungthanhnguyen0502"
            user.birthday   = new Date()
            result.push( user)
        }
        return result
    }
})

myApp.service('commentService', ['$http' , function($http){
    this.getComments = function( ticket_id){
        // url = ""
        // $http.get(url, {params : { ticket_id:ticket_id}})
        //     .success()
        //     .error()
    }
    this.createComment = function( data){
        // url = ""
        // $http.post( url , data).success( function(){
            
        // }).error(function(){

        // });
    }
}])








myApp.controller('userHeaderController', ['$scope' , '$rootScope' , function($scope , $rootScope){
    $scope.user = $rootScope.user
}])


myApp.controller('sideBarController',['$scope', function($scope){
    $scope.name = ""
    $scope.show = false
    $scope.changeShow = function(){
        $scope.show = ! $scope.show
    }

    
}])

myApp.controller('dashBoardController'  , ['$scope','$stateParams','ticketService' , '$rootScope', 'maps', 'fakeDataService', function($scope,$stateParams, ticketService , $rootScope,maps, fakeDataService){
    
    $scope.name                     = $stateParams.name
    $scope.status                   = $stateParams.condition
    $scope.condition                = new Condition()
    $scope.tickets = fakeDataService.fakeTickets(1 , 10)
    $scope.paginate_params          = new PaginatePrams()
    
    $scope.search_data  = {
        subject: null,
        creat_by: null
    }          




    for( i = 0 ; i < $scope.tickets.length ;i++){
        $scope.tickets[i].index = i+1
    }


    $scope.searchSubject = function( subject){
        $scope.search_data.subject = ticketService.SearchSubject(subject)  
    }

    $scope.getTickets = function( condition = $scope.condition){
        if( $scope.status != 'all' && maps.ticket_status[condition.status] !=  $scope.status  )
            alert("không thể tìm kiếm trạng thái khác")
        else    
            $scope.tickets = ticketService.getTickets( condition)
    }

    $scope.changeRead = function( ticket){
        data = { id: ticket.id , is_read: ! ticket.is_read}
        ticketService.editTicket(data)
        ticket.is_read = !ticket.is_read
    }
   
    $scope.reload = function(){
        $scope.resetCondition()
        $scope.initCondition()
    }

    $scope.initCondition = function(){
        if( $scope.name == "my_request"){
            $scope.condition.create_by = $rootScope.user.id
        }
        if( $scope.name == "related_request"){
            $scope.condition.related_employee_id = $rootScope.user.id
        }
        if( $scope.name == "mission"){
            $scope.condition.employee_id = $rootScope.user.id
        }
        if( $scope.name == "team_request"){
            $scope.condition.team_id = $rootScope.user.team_id
        }
        if( $scope.status == 'inprogress')
             $scope.condition.status = 2 
        if( $scope.status == 'resolved')
             $scope.condition.status = 3
        if( $scope.status == 'out_of_date')
             $scope.condition.status = 7
        
    }

    $scope.resetCondition = function(){
        $scope.condition = new Condition()
        $scope.initCondition()
    }

    $scope.getStatus = function(){
        return [1,2,3,4,5,6]
    }
    $scope.getPriority = function(){
        return [1,2,3,4,5]
    }
    $scope.showStatusSelect = function(){
        return $scope.status == 'all'
    }

    $scope.getUser  = function( input){
                
    }
    $scope.initCondition()
}])



myApp.controller('ticketDetailController' , ['$scope' , '$stateParams','ticketService','$filter', 'commentService', 'fakeDataService', '$sce','$rootScope', function( $scope , $stateParams, ticketService, $filter, commentService, fakeDataService, $sce, $rootScope){
    $scope.id = $stateParams.id
    
    // $scope.ticket = ticketService.getTicket($scope.id)
    //fake data
    $scope.ticket = fakeDataService.fakeTickets(1 , 1)[0]

    // $scope.comments = commentService.getComments($scope.ticket.id)
    $scope.comments = fakeDataService.fakeComments(1 , $scope.ticket_id ,5)

    $scope.commentContent = ""
    $scope.commentInput = null
    $scope.oldInfo = {}
    $scope.newInfo = {}
   
   
    

   



    $scope.saveChange =  function(){
        console.log($scope.oldInfo)
        console.log($scope.newInfo)
        if(! $scope.commentContent ){
            alert("chưa nhập lý do thay đổi")
            $scope.newInfo = jQuery.extend(true, {}, $scope.oldInfo);
            return;
        }
            data = {}
            data.id = $scope.ticket.id
            if($scope.oldInfo.status != $scope.newInfo.status){
                data.status = $scope.newInfo.status
                $scope.commentContent = "thay đổi trạng thái: " + $filter('toTicketStatus')($scope.oldInfo.status) + "=> " +   $filter('toTicketStatus')($scope.newInfo.status) + 
                " <br/> lí do : " + $scope.commentContent
            }
            if($scope.oldInfo.priority != $scope.newInfo.priority){
                data.priority = $scope.newInfo.priority
                $scope.commentContent = "thay đổi mức độ ưu tiên: " + $filter('toPriority')($scope.oldInfo.priority) + "=> " +   $filter('toPriority')($scope.newInfo.priority) +
                " <br/> lí do : " + $scope.commentContent
            }
            if($scope.oldInfo.deadline != $scope.newInfo.deadline){
                data.deadline = $scope.newInfo.deadline
                $scope.commentContent = "thay đổi deadline: " + ($scope.oldInfo.deadline).toString() + "=> " +  ($scope.newInfo.deadline).toString() +
                "<br/> lí do : " + $scope.commentContent
            }
            if($scope.oldInfo.relatedUser != $scope.newInfo.relatedUser){
                data.status = $scope.newInfo.status
                $scope.commentContent = "thay đổi người liên quan: " + $filter('')($scope.oldInfo.status) + "=> " +   $filter('')($scope.newInfo.status) +
                "\n lí do : " + $scope.commentContent
            }
            if($scope.oldInfo.team_id != $scope.newInfo.team_id){
                data.team_id = $scope.newInfo.team_id
                $scope.commentContent = "thay đổi đôi IT: " + $filter('toTeam')($scope.oldInfo.team_id) + "=> " +   $filter('toTeam')($scope.newInfo.team_id) +
                "<br/> lí do : " + $scope.commentContent
            }

            success = ticketService.editTicket( data)
            if( success ){
                $scope.ticket = ticketService.getTicket( $scope.ticket.id)
                $scope.initNewInfo()
                // $scope.comments = commentService.getComments( $scope.ticket.id)
            }
            else{
                $scope.initNewInfo()
            }

            //fakeData 
            $scope.commentContent = $sce.trustAsHtml($scope.commentContent);
    
            $scope.comments.push( fakeDataService.fakeComments(11 , $scope.ticket.id , 1, $scope.commentContent)[0])
            //
            $scope.commentContent = ""
            $scope.initNewInfo()
    }
    $scope.createComment = function(){
        if( !$scope.commentInput){
            alert("nội dung trống")
            return;
        }
        data = { id:$scope.ticket.id, content: $scope.commentInput , user_id: $rootScope.user.id }
        commentService.createComment(data)
        $scope.comments.push( fakeDataService.fakeComments(1,data.id , 1 , data.content)[0])
        $scope.commentInput = null
    }


    $scope.getStatus = function(){
        return [1,2,3,4,5,6]
    }
    $scope.getPriority = function(){
        return [1,2,3,4,5]
    }
    $scope.show = function(data){
        alert(data)
    }
    $scope.changeNewPriority = function( priority){
        $scope.newInfo.priority = priority
    }
    $scope.changeNewStatus = function( status){
        $scope.newInfo.status = status
    }
    $scope.changeNewDeadline = function( deadline){
        newDeadline = deadline
    }
    $scope.initNewInfo = function(){
        $scope.oldInfo = { 
            status: $scope.ticket.status,
            priority: $scope.ticket.priority,
            deadline: $scope.ticket.deadline,
            // relatedUser:ticketService.getRelatedUser(),
            assigned_to: $scope.ticket.assigned_to,
            team_id: $scope.ticket.team_id
        }
        $scope.newInfo = jQuery.extend(true, {}, $scope.oldInfo);
    }
    $scope.initNewInfo()
}])
    


myApp.controller('newRequestController' , ['$scope' , 'ticketService','$rootScope' , 'commentService',function($scope , ticketService , $rootScope, commentService){
    $scope.user = $rootScope.user
    $scope.ticket = new Ticket()
    //fake data
    $scope.ticket.deadline = new Date()

    $scope.save = function(){
        if( !$scope.ticket.priority || !$scope.ticket.content)
                console.log("dữ liệu bị thiếu")
            else {
                ticketService.saveTicket( $scope.ticket)
            }
    }

    $scope.loadComment = function(){
    }


}])
