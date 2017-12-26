var myApp = angular.module("myApp", ['ui.router', 'ngRoute', 'naif.base64', 'ngSanitize'])

myApp.value('maps', {
    'priority': {1: 'Thấp', 2: 'Bình thường', 3: 'Cao', 4: 'Khẩn cấp'},
    'ticket_status': {
        1: 'Mới',
        2: 'Đang xử lý',
        3: 'Đã xong',
        4: 'Phản hồi',
        5: 'Đã đóng',
        6: 'Đã hủy',
        7: 'Quá thời hạn'
    },
    'rating': {0: 'Không hài lòng', 1: 'Hài lòng'},
    'type': {0: 'không', 1: 'đánh giá', 2: 'thay đổi độ ưu tiên', 3: 'thay đổi deadline'},
    'ticket_read_status': {0: 'chưa đọc', 1: 'đã đọc'},
    'team': {1: 'Hà Nội-IT', 2: 'Đà Nẵng-IT '}
})


myApp.value('EV_dictionary', {
    'my_request': 'Việc tôi yêu cầu',
    'related_request': 'Công việc liên quan',
    'mission': 'Nhiệm vụ được giao',
    'team_request': 'Công việc của nhóm'
})

myApp.config(function ($stateProvider, $urlRouterProvider) {

    $stateProvider
        .state('home', {
            url: '/home',
            templateUrl: './app/components/Home/home.html',
        })
    $stateProvider
        .state('dashBoard', {
            url: '/dash_board/:name/:condition',
            templateUrl: 'app/components/dashboard/dashboard.html',
            controller: 'dashBoardController',
        })


    $stateProvider
        .state('ticketDetail', {
            url: '/ticket/:id',
            templateUrl: 'app/components/ticketDetail/ticketDetail.html',
            controller: 'ticketDetailController'
        })

    $urlRouterProvider.otherwise('/dash_board/my_request/all')
})

myApp.run(['$rootScope', '$http', 'userService', 'fakeDataService', function ($rootScope, $http, userService, fakeDataService) {
    $rootScope.info = {}
    $http.get('/api/employee-info').then(function (response) {
        $rootScope.info.user = new User(response.data)
    }, function () {
        alert("thông tin đăng nhập không đúng")
    })
}])


myApp.component('sideBar', {
    templateUrl: './app/components/SideBar/sideBar.html',
    controller: 'sideBarController',
    bindings: {
        name: '=name',
    },
})
myApp.component('newRequest', {
    templateUrl: './app/components/newRequest/newRequest.html',
    controller: 'newRequestController',
    bindings: {
        ticket: '=',
    }
})
myApp.component('myFooter', {
    templateUrl: './app/common/footer.html',
})

myApp.component('myHeader', {
    templateUrl: './app/common/userHeader.html',
    controller: 'headerController'
})


myApp.filter('underline', function () {
    return function (input) {
        return input.replace(" ", "_")
    }
})
myApp.filter('toNomal', function () {
    return function (input) {
        input = input.charAt(0).toUpperCase() + input.slice(1)
        return input.replace("_", " ")
    }
})


myApp.filter('toPriority', ['mapService', function (mapService) {
    return function (input) {
        result = mapService.map(input, 'priority')
        return result
    }
}])
myApp.filter('toTicketStatus', ['mapService', function (mapService) {
    return function (input) {
        return mapService.map(input, 'ticket_status')
    }
}])
myApp.filter('toRating', ['mapService', function (mapService) {
    return function (input) {
        return mapService.map(input, 'rating')
    }
}])
myApp.filter('toTeam', ['mapService', function (mapService) {
    return function (input) {
        return mapService.map(input, 'team')
    }
}])
myApp.filter('toTypeComment', ['mapService', function (mapService) {
    return function (input) {
        return mapService.map(input, 'type')
    }
}])
myApp.filter('toReadStatus', ['mapService', function (mapService) {
    return function (input) {
        return mapService.map(input, 'ticket_read_status')
    }
}])
myApp.filter('VietNamTrans', ['EV_dictionary', function (EV_dictionary) {
    return function (input) {
        return EV_dictionary[input]
    }
}])


myApp.service('mapService', ['maps', function (maps) {
    this.map = function (input, map_name) {
        return maps[map_name][input]
    }
}])


myApp.service('conditionFilterService', function () {
    this.filterCondition = function (condition) {
        if (condition.deadline && condition.deadline.getFullYear() == 1899)
            condition.deadline = null
        result = {}

        for (var pro in condition) {
            if (condition[pro])
                result[pro] = condition[pro]
        }


        return result
    }
})

myApp.service('ticketService', ['conditionFilterService', '$http', function (conditionFilterService, $http) {


    this.getTickets = function (condition, tickets) {
        condition.status = parseInt(condition.status)
        condition.priority = parseInt(condition.priority)
        condition = conditionFilterService.filterCondition(condition)

        tickets.length = 0
        
        $http.get("/api/get-tickets", {params: condition}).then(function (response) {
            i = 1
            for (index in response.data) {
                t = new Ticket(response.data[index])
                t.index = i
                i += 1
                tickets.push(t)
            }
       }, function () {
        })
    }

    this.changeRead = function( input){
        $http.post("/api/read-ticket" ,data).then( function( response){
            console.log( response)
        }, function(){
            alert("faild")
        })
    }

    this.saveTicket = function (ticket) {
        data = {}
        for (var pro in ticket) {
            if (ticket[pro])
                data[pro] = ticket[pro]
        }
        console.log(data)
        $http.post("/api/create-ticket", data).then(function (response) {
            alert("success!");
        }).catch(function (response) {
            console.log(response.message)
        });
    }

    this.checkTicket = function (ticket) {
        if (ticket.status == -1 || ticket.priority == -1 || ticket.rating == -1 || ticket.team_id == -1) {
            return false
        }
        return true
    }
    this.editTicket = function (ticket) {
        if (!this.checkTicket(ticket) || ticket.id == -1 || !ticket.id)
            alert("data is wrong")
        else {
            data = ticket
            $http.put("/api/edit-ticket", params = ticket).success(function (response) {
                alert("success!");
            }).error(function (response) {
                alert("failed!");
            });
        }
    }

    this.getRelatedUser = function (ticket_id) {
        $http.get("", {id: ticket_id}).success(function (response) {
            return
        }).error(function (response) {
            alert("dữ liệu bị lỗi")
        })
    }

}])

myApp.service('userService', ['$http', 'fakeDataService', function ($http, fakeDataService) {

    this.searchName = function (input, output) {
        output.length = 0

        $http.post('/api/search-employee', {name: input}).then(function (response) {
            for (index in response.data) {
                user = {}
                user.id = response.data[index].id
                user.user_name = response.data[index].display_name
                output.push(user)
            }
        }, function () {
            alert("thông tin không chính xác")
        })
    }
}])


myApp.service('fakeDataService', ['ticketService', function (ticketService) {
    this.fakeTickets = function (number) {
        for (i = 50; i < 50 + number; i++) {
            t = new Ticket()
            t.subject = "công việc thứ " + i.toString()
            t.content = "nội dung công việc thứ " + i.toString()
            t.deadline = new Date()
            t.team_id = i % 2 == 0 ? 1 : 0
            t.is_read = i % 2 == 0 ? 1 : 0
            t.assigned_to = i
            t.status = Math.floor(Math.random() * 5) + 1;
            t.priority = Math.floor(Math.random() * 4) + 1
            t.is_read = i % 2 == 0 ? 1 : 0
            ticketService.saveTicket(t)
        }
    }
    this.fakeComments = function (first_id, ticket_id, number, content) {
        result = []
        for (var i = first_id; i < first_id + number; i++) {
            comment = new Comment()
            comment.id = i
            comment.ticket_id = ticket_id
            comment.create_at = new Date()
            comment.user_id = Math.floor(Math.random() * 5) + 1;
            comment.content = content ? content : " this is content comment"
            comment.user_name = i % 2 == 0 ? " nguyễn Thành Trung " : "Trung Thành Nguyễn "
            comment.created_at = new Date()
            result.push(comment)
        }
        return result
    }
}])

myApp.service('commentService', ['$http', function ($http) {
    
    this.loadComment = function (ticket_id , output) {
        $http.get("/api/get-comments/"+ ticket_id).then( function(response){
            output.length = 0 
            for( index in response.data){                
                output.push( new Comment( response.data[index]))
            }  
        } , function( response){

        } )
    }
    this.createComment = function (data) {
        console.log(data)
        $http.post('/api/comment', params = data).then(function ( response) {
                console.log( response)
            }
            , function (response) {
                alert("lỗi" + response.messages)
            })
    }
}])


myApp.controller('sideBarController', ['$scope' , 'ticketService', '$http', 'conditionFilterService', function ($scope , ticketService, $http, conditionFilterService) {

    $scope.show = false
    $scope.name = $scope.$ctrl.name

    $scope.number = {
        allNumber:0,
        inprogressNumber : 0,
        resolvedNumber : 0,
        outOfDateNumber :0,
        feedbackNumber : 0,
        closedNumber: 0
    }
   

    $scope.initCondition = function(){
        $scope.condition = new Condition()
        $scope.condition.count = 1
        if ($scope.name == "my_request") {
            $scope.condition.selector = "created_by"
        }
        if ($scope.name == "related_request") {
            $scope.condition.selector = "related_to"
        }
        if ($scope.name == "mission") {
            $scope.condition.selector = "assigned_to"
        }
        if ($scope.name == "team_request") {
            $scope.condition.selector = "team_id"
        }

    }

    $scope.initCondition()


    $scope.getTicketsCount = function( condition , outputName){
        condition = conditionFilterService.filterCondition(condition)
        $http.get('/api/get-tickets' , {params: condition}).then( function( response){
            $scope.number[outputName] = response.data
        } , function(){
        })
    }
    
    
    $scope.getTicketsCount( $scope.condition ,'allNumber')

    $scope.condition.status = 2
    $scope.getTicketsCount( $scope.condition ,'inprogressNumber')

    $scope.condition.status = 3
    $scope.getTicketsCount( $scope.condition ,'resolvedNumber')

    $scope.condition.status = 4
    $scope.getTicketsCount( $scope.condition ,'feedbackNumber')

    $scope.condition.status = 5
    $scope.getTicketsCount( $scope.condition ,'closedNumber')

    $scope.condition.status = 7
    $scope.getTicketsCount( $scope.condition ,'outOfDateNumber')
   



    // $http.get("/api/get-tickets", {params: condition}).then(function (response) {
    //     i = 1
    //     for (index in response.data) {
    //         t = new Ticket(response.data[index])
    //         t.index = i
    //         i += 1
    //         tickets.push(t)
    //     }

    // }, function () {
    //     alert("tìm kiếm thất bại")
    // })

    // $scope.getTicketNumber = function( ){

    // }


    $scope.changeShow = function () {
        $scope.show = !$scope.show
    }
}])

myApp.controller('dashBoardController', ['$scope', '$stateParams', 'ticketService', '$rootScope', 'maps', 'fakeDataService', 'userService', '$http', function ($scope, $stateParams, ticketService, $rootScope, maps, fakeDataService, userService, $http) {

    $scope.name = $stateParams.name
    $scope.status = $stateParams.condition
    $scope.condition = new Condition()

    $scope.paginate_params = new PaginatePrams()
    $scope.tickets = []

    $scope.search_data = {
        subject: null,
        user_recommend: []
    }


    $scope.text_input = {
        created_by: null,
        subject: null,
        assign_to: null
    }


    $scope.getTickets = function () {
        $scope.condition.page = $scope.paginate_params.current_page
        $scope.condition.per_page = $scope.paginate_params.page_size
        ticketService.getTickets($scope.condition, $scope.tickets)
        $scope.initCondition()

        $scope.condition.count = 1
        $scope.condition.deadline = null
        $http.get('/api/get-tickets' , {params: $scope.condition}).then( function( response){
            $scope.paginate_params.total = Math.ceil( response.data / $scope.paginate_params.page_size )
        } , function(){
        })

        $scope.initCondition()
        

    }

    $scope.changeRead = function( ticket){
        if( ticket.is_read == 0 )
            ticket.is_read = 1
        else
            ticket.is_read = 0

        data = {ticket_id:ticket.id , read: ticket.is_read}
        ticketService.changeRead(data)

    }

    $scope.initCondition = function () {

        $scope.condition = new Condition()
        if ($scope.name == "my_request") {
            $scope.condition.selector = "created_by"
        }
        if ($scope.name == "related_request") {
            $scope.condition.selector = "related_to"
        }
        if ($scope.name == "mission") {
            $scope.condition.selector = "assigned_to"
        }
        if ($scope.name == "team_request") {
            $scope.condition.selector = "team_id"
        }

        if ($scope.status == 'inprogress')
            $scope.condition.status = 2
        if ($scope.status == 'resolved')
            $scope.condition.status = 3
        if ($scope.status == 'out_of_date')
            $scope.condition.status = 7
        if ($scope.status == 'feedback')
            $scope.condition.status = 4
        if ($scope.status == 'closed')
            $scope.condition.status = 5

    }


    $scope.getStatus = function () {
        return [1, 2, 3, 4, 5, 6]
    }

    $scope.getPriority = function(){
        return [1,2,3,4]
    }
    $scope.showStatusSelect = function () {
        return $scope.status == 'all'
    }


    $scope.searchName = function (name) {
        if (typeof(name) == 'string' && name.length > 0 && name.length % 2 == 0) {
            $scope.search_data.user_recommend.length = 0
            userService.searchName(name, $scope.search_data.user_recommend)
        }
    }

    $scope.getUser = function (input) {
    }
    $scope.initCondition()

    $scope.getTickets()

    
}])


myApp.controller('ticketDetailController', ['$scope', '$stateParams', 'ticketService', '$filter', 'commentService', 'fakeDataService', '$sce', '$rootScope', 'userService', '$http', function ($scope, $stateParams, ticketService, $filter, commentService, fakeDataService, $sce, $rootScope, userService, $http) {

    $scope.id = $stateParams.id
    $scope.commentInput = null
    $scope.commentInput2 = null
    $scope.info = $rootScope.info
    $scope.user_recommend = []
    $scope.comments = []
    $scope.oldInfo = {}
    $scope.newInfo = {}
    $scope.evaluate = false
    $scope.rate    = 1
    
    $scope.assign_to_input = { id: null, user_name: null}

    $scope.saveChange = function () {
        if (!$scope.commentInput) {
            alert("chưa nhập lý do thay đổi")
            $scope.newInfo = jQuery.extend(true, {}, $scope.oldInfo);
            return;
        }
        data = {}

        
        data.ticket_id = $scope.ticket.id
        if($scope.oldInfo.status != $scope.newInfo.status){
            data.status = parseInt( $scope.newInfo.status)
            data.note =  "thay đổi trạng thái: " + $filter('toTicketStatus')($scope.oldInfo.status) + "=> " +   $filter('toTicketStatus')($scope.newInfo.status) + 
            "----- lí do : " + $scope.commentInput
    
            $http.put('api/edit-ticket/status' , params=data).then( function(response){
                if( response.data.status == 1 ) {
                    $scope.getTicket() 
                        alert("thay đổi trạng thái thành công")
                    console.log( $scope.newInfo.status )
                    if( $scope.newInfo.status == 3){
                        $http.post('api/rate/'+ data.ticket_id , params = { rating: rate}).then( function(){
                            console.log( response)
                        } , function(){

                        })
                    }
                }
                else
                    alert(response.data.phrase)
                } , 
                function(){
                    alert("thông tin thay đổi không đúng")
                })
        }
        if ($scope.oldInfo.priority != $scope.newInfo.priority) {
            data.priority = $scope.newInfo.priority
            data.note  =  "thay đổi mức độ ưu tiên: " + $filter('toPriority')($scope.oldInfo.priority) + "=> " +   $filter('toPriority')($scope.newInfo.priority) +
            "-----  lí do : " +$scope.commentInput
            $http.put('api/edit-ticket/priority' , params=data).then( function(response){
                if( response.data.status == 1){
                    $scope.getTicket()
                    alert("thay đổi độ ưu tiên thành công")
                }
                
            } , 
            function(){
                alert("thông tin thay đổi không đúng")
            })
        }
        if ($scope.oldInfo.deadline != $scope.newInfo.deadline) {
            data.deadline = $scope.newInfo.deadline
            data.note =  ("thay đổi deadline: " + ($scope.oldInfo.deadline).toString() + "=> " +  ($scope.newInfo.deadline).toString() +
            "----- lí do : " + $scope.commentInput)
            $http.put('api/edit-ticket/deadline' , params=data).then( function(response){
                if( response.data.status == 1){
                    $scope.getTicket()
                    alert("thay đổi deadline thành công")
                }
                else
                    alert( response.data.phrase)
            } , 
            function( message ){
                alert("không hợp lệ")
            })

        }
       
        if ($scope.oldInfo.team_id != $scope.newInfo.team_id) {
            data.team_id = $scope.newInfo.team_id
            data.note = ("thay đổi đôi IT: " + $filter('toTeam')($scope.oldInfo.team_id) + "=> " + $filter('toTeam')($scope.newInfo.team_id) +
                "----- lí do : " + $scope.commentInput)
            $http.put('api/edit-ticket/team', params = data).then(function (response) {
                if( response.data.status == 1)
                {
                    alert("thay đổi đôi IT thành công")
                    $scope.getTicket()
                }
                else
                    alert(response.data.phrase) 
                },
                function () {
                    alert("thông tin thay đổi không đúng")
                })
        }
        $scope.commentInput = null
        $scope.initNewInfo()
    }

    $scope.createComment = function () {
        if (!$scope.commentInput2) {
            alert("nội dung trống")
            return;
        }
        data = {ticket_id: $scope.ticket.id, content: $scope.commentInput2}
        commentService.createComment(data)
        $scope.loadComment()
        $scope.commentInput2 = null
    }

    $scope.loadComment = function(){
        commentService.loadComment( $scope.ticket.id , $scope.comments )
    }


    $scope.getStatus = function () {
        return [1, 2, 3, 4, 5, 6]
    }
    $scope.getPriority = function(){
        return [1,2,3,4]
    }
    $scope.changeNewPriority = function (priority) {
        $scope.newInfo.priority = priority
    }
    $scope.changeNewStatus = function (status) {
        if( status == 3 )
            $scope.evaluate = true
        else
          $scope.evaluate = false
        $scope.newInfo.status = status
    }
    $scope.changeNewDeadline = function (deadline) {
        newDeadline = deadline
    }
    $scope.initNewInfo = function () {
        $scope.oldInfo = {
            status: $scope.ticket.status,
            priority: $scope.ticket.priority,
            deadline: $scope.ticket.deadline,
            assigned_to: $scope.ticket.assigned_to,
            team_id: $scope.ticket.team_id
        }
        $scope.newInfo = jQuery.extend(true, {}, $scope.oldInfo);
    }

    $scope.searchName = function (name) {
        $scope.user_recommend = []
        if (typeof(name) == 'string' && name.length > 0 && name.length % 2 == 0) {
            userService.searchName(name, $scope.user_recommend)
        }
    }

    $scope.changeRelater = function(){
        data = {}
        data.ticket_id = $scope.ticket.id
        data.relaters = []
        for( var u in $scope.ticket.relaters){
            data.relaters.push( $scope.ticket.relaters[u].id )
        }
        console.log(data)
        data.relaters = angular.toJson( data.relaters)
        data.note  = ("thay đổi người liên quan: "+ "----- lí do : " +$scope.commentInput)
        $http.post('/api/edit-relaters' , params=data).then( function(response){
            if( response.data.status == 1){
                $scope.getTicket()
                alert("thay đổi người liên quan thành công")
            }
            else
                alert(response.data.phrase)
        } , 
        function(){
            alert("không thành công")
        })
    }

    $scope.addRelatedUser = function( user ){
        if(user){
            shortcutName = user.user_name.split(" ")
            user.user_name = shortcutName[shortcutName.length-1]
            $scope.ticket.relaters.push(user);
            $scope.inputName = ""
            $scope.user_recommend = []
        }
    }
    $scope.removeRelatedUser = function(id){
        for( var u in $scope.ticket.relaters){
            if(  $scope.ticket.relaters[u].id == id )
                $scope.ticket.relaters.pop(u)
        }
    }

    $scope.addAssignTo = function( user ){
        $scope.inputName = null
        $scope.assign_to_input = user
        console.log( $scope.assign_to_input)
    }

    


    $scope.changeAssign = function(){
        data = {}
        data.ticket_id = $scope.ticket.id
        data.employee_id = $scope.assign_to_input.id
       
        console.log(data)

        $http.put('api/edit-ticket/assigned_to' , params=data).then( function(response){
            if( response.data.status == 1){
                $scope.getTicket()
                alert("thay đổi người thực hiện thành công")
            }
            else
                alert(response.data.phrase)
        } , 
        function(){
            alert("không thành công")
        })
    }


    $scope.deleteAssignTo = function(){
        $scope.assign_to_input = {}
    }



    $scope.getTicket = function(){
        $http.get("/api/get-ticket/" + $scope.id.toString()).then(function (response) {
            $scope.ticket = new Ticket(response.data)
            $scope.initNewInfo()
            $scope.loadComment()
        }, function () {
            alert("không tồn tại ")
        })
    }
    $scope.getTicket()
  
}])


myApp.controller('newRequestController', ['$scope', 'ticketService', '$rootScope', 'commentService', 'userService', function ($scope, ticketService, $rootScope, commentService, userService) {
    $scope.user = $rootScope.user
    $scope.ticket = new Ticket()
    $scope.ticket.relaters = []
    $scope.ticket.deadline = new Date()
    $scope.user_recommend = []
    $scope.inputName = ""
    $scope.save = function () {
        temp = []
        for( var u in $scope.ticket.relaters){
            temp.push(  $scope.ticket.relaters[u].id)
        }
        $scope.ticket.relaters = angular.toJson(  $scope.ticket.relaters)

        $scope.ticket.relaters = temp
        console.log($scope.ticket.relaters)
        if (!$scope.ticket.priority || !$scope.ticket.content || !$scope.ticket.team_id)
            alert("dữ liệu bị thiếu")
        else {
            ticketService.saveTicket($scope.ticket)
        }
    }

    $scope.loadComment = function () {
    }

    $scope.searchName = function (name) {
        if (typeof(name) == 'string' && name.length > 0 && name.length % 2 == 0) {
            userService.searchName(name, $scope.user_recommend)
        }
    }
    $scope.addRelatedUser = function( user ){

        if(user){
            shortcutName = user.user_name.split(" ")
            user.user_name = shortcutName[shortcutName.length-1]
            $scope.ticket.relaters.push(user);
            $scope.inputName = ""
            $scope.user_recommend = []
        }
    }
    $scope.removeRelatedUser = function(id){
        for( var u in $scope.ticket.relaters){
            if(  $scope.ticket.relaters[u].id == id )
                $scope.ticket.relaters.pop(u)
        }
    }



}])


myApp.controller('headerController', ['$scope', '$rootScope', function ($scope, $rootScope) {
    $scope.info = $rootScope.info
}])


myApp.controller('AppController' , ['$scope' ,'$rootScope', function ($scope, $rootScope) {
    $scope.info = $rootScope.info
}])