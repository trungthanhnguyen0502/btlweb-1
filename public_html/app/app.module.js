var myApp = angular.module("myApp" , ['ui.router', 'ngRoute']);
myApp.value('maps', {
    'priority'     :{ 1: 'thấp' , 2:'bình thường' , 3: 'cao' , 4: 'feedback' , 5:'khẩn cấp'},
    'ticket_status':{1: 'new' , 2:'inprogress' ,3:'resolved', 4:'feedback', 5:'closed' , 6:'cancelled'},
    'rating'       :{0:'không hài lòng' , 1:'hài lòng'},
    'type'         :{0:'không', 1:'đánh giá' , 2:'thay đổi độ ưu tiên' , 3:'thay đổi deadline'},
    'ticket_read_status'    :{0:'chưa đọc' , 1:'đã đọc'}
});




myApp.config(function($stateProvider, $urlRouterProvider) {
    
    $stateProvider
        .state('home', {
            url: '/home',
            templateUrl: './app/components/Home/home.html',
        });
    $stateProvider
        .state('dashBoard' , {
            url: '/dash_board/:name',
            templateUrl:'app/components/dashboard/dashboard.html',
            controller: 'dashBoardController',
        });


    $stateProvider
        .state('ticketDetail' , {
            url: '/ticket/:id',
            templateUrl:'app/components/ticketDetail/ticketDetail.html',
            controller: 'ticketDetailController'
        });
    // $stateProvider
    //     .state('' , {
    //         url: '',
    //         templateUrl:'',
    //     });
    // $stateProvider
    //     .state('' , {
    //         url: '',
    //         templateUrl:'',
    //     });

    $urlRouterProvider.otherwise('/dash_board/my_request');
});
    

















myApp.component('sideBar',{
    templateUrl: './app/components/SideBar/sideBar.html',
    controller: 'sideBarController',
    bindings:{
        name: '=',
    },
});
myApp.component('newRequest', {
    templateUrl: './app/components/newRequest/newRequest.html',
    controller: 'newRequestController',
    bindings:{
        ticket: '=',
    }
});







myApp.filter('underline', function(){
    return function(input){
        return input.replace(" " , "_");
    };
});
myApp.filter('toNomal' , function(){
    return function(input){
        input = input.charAt(0).toUpperCase() + input.slice(1);
        return input.replace("_" ," ");
    }
});













myApp.filter('toPriority', ['mapService' , function( mapService){
    return function(input){
        return mapService.map(input , 'priority');
    }
}]);
myApp.filter('toTicketStatus', ['mapService' , function( mapService){
    return function(input){
        return mapService.map(input , 'ticket_status');
    }
}]);
myApp.filter('toRating', ['mapService' , function( mapService){
    return function(input){
        return mapService.map(input , 'rating');
    }
}]);
myApp.filter('toTypeComment', ['mapService' , function( mapService){
    return function(input){
        return mapService.map(input , 'type');
    }
}]);
myApp.filter('toReadStatus', ['mapService' , function( mapService){
    return function(input){
        return mapService.map(input , 'ticket_read_status');
    }
}]);











myApp.service('mapService' , [ 'maps' , function(maps){
    this.map = function( input,map_name){
         return maps[map_name][input]
    }
}]);

myApp.service('ticketService', function(){
    this.getTicket = function(id){
        //fake data
        ticket = new Ticket();
        ticket.id = id;
        return ticket
    }
    this.getTickets = function(){
        return 1;
    }
    this.deleteTickets = function(id){
    }
    this.newTicket = function(data){
        if( typeof(data) == "string")
            data = angular.fromJson(data);
        if( angular.isArray(data)){
            Tickets = [];
            for( var obj in data){
                Tickets.push( new Ticket(obj));
            }
            return Tickets;
        }
        else{
            return new Ticket(data)
        }
    }

    this.saveTicket = function(ticket){
        alert("save ticket");
    }
    
    this.checkTicket = function( ticket){
        if( ticket.creat_by == -1 || ticket.status == -1 
            || ticket.priority == -1 || ticket.rating == -1 || ticket.team_id == -1 
        ){
            return false
        }
        return true
    }




    this.fakeData = function(_id){
        data = {id: _id,
                subject:"this is subject "+ _id.toString(),
                content:"this is content",
                creat_by:1
        };
        return new Ticket(data);
    }
    this.fakeDataList = function(dataNumber){
        dataList = [];
        for( i = 0; i < dataNumber; i++ )
            dataList.push( this.fakeData(i));
        return dataList;
    }
});






myApp.controller('sideBarController',['$scope', function($scope){
    $scope.name = "";
}]);
myApp.controller('newRequestController' , ['$scope' , 'ticketService',function($scope , ticketService){
    $scope.ticket = new Ticket();
    $scope.save = function(){
        console.log($scope.ticket)
        if (ticketService.checkTicket( $scope.ticket )){
            ticketService.saveTicket( $scope.ticket)
        }
        else{
            alert('fail to create new ticket');
        }
    }
}]);

myApp.controller('dashBoardController' , ['$scope','$stateParams','ticketService', function($scope,$stateParams , ticketService){
    $scope.name = $stateParams.name;
    $scope.tickets = ticketService.fakeDataList(10);
}]);
myApp.controller('ticketDetailController' , ['$scope' , '$stateParams','ticketService' , function( $scope , $stateParams, ticketService){
    $scope.id = $stateParams.id;
    $scope.ticket = ticketService.getTicket($scope.id);
}]);
    