var myApp = angular.module("myApp", ['ui.router', 'ngRoute', 'naif.base64', 'ngSanitize'])

myApp.value('maps', {
    'priority': {1: 'thấp', 2: 'bình thường', 3: 'cao', 4: 'feedback', 5: 'khẩn cấp'},
    'ticket_status': {
        1: 'new',
        2: 'inprogress',
        3: 'resolved',
        4: 'feedback',
        5: 'closed',
        6: 'cancelled',
        7: 'out_of_date'
    },
    'rating': {0: 'không hài lòng', 1: 'hài lòng'},
    'type': {0: 'không', 1: 'đánh giá', 2: 'thay đổi độ ưu tiên', 3: 'thay đổi deadline'},
    'ticket_read_status': {0: 'chưa đọc', 1: 'đã đọc'},
    'team': {0: 'Hà Nội-IT', 1: 'Đà Nẵng-IT '}
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
    // fakeDataService.fakeTickets(100)
}])

myApp.directive('uploadFiles', function () {
    return {
        scope: true,        //create a new scope  
        link: function (scope, el, attrs) {
            el.bind('change', function (event) {
                var files = event.target.files
                for (var i = 0; i < files.length; i++) {
                    scope.$emit("seletedFile", {file: files[i]})
                }
            })
        }
    }
})


myApp.component('sideBar', {
    templateUrl: './app/components/SideBar/sideBar.html',
    controller: 'sideBarController',
    bindings: {
        name: '=',
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

//filter
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
        console.log(condition)
        $http.get("/api/get-tickets", {params: condition}).then(function (response) {
            i = 1
            for (index in response.data) {
                t = new Ticket(response.data[index])
                t.index = i
                i += 1
                tickets.push(t)
            }

        }, function () {
            alert("tìm kiếm thất bại")
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
        $http.post('/api/search-employee', {name: input}).then(function (response) {
            console.log(response.data)
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
    this.getComments = function (ticket_id) {
        // url = ""
        // $http.get(url, {params : { ticket_id:ticket_id}})
        //     .success()
        //     .error()
    }
    this.createComment = function (data) {
        console.log(data)
        $http.post('/api/comment', params = data).then(function () {

            }
            , function (response) {
                alert("lỗi" + response.messages)
            })
    }
}])


myApp.controller('sideBarController', ['$scope', function ($scope) {
    $scope.name = ""
    $scope.show = false
    $scope.changeShow = function () {
        $scope.show = !$scope.show
    }


}])

myApp.controller('dashBoardController', ['$scope', '$stateParams', 'ticketService', '$rootScope', 'maps', 'fakeDataService', 'userService', function ($scope, $stateParams, ticketService, $rootScope, maps, fakeDataService, userService) {

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

    }

    $scope.changeRead = function (ticket) {
        data = {id: ticket.id, is_read: !ticket.is_read}
        ticketService.editTicket(data)
        ticket.is_read = !ticket.is_read
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
    $scope.getPriority = function () {
        return [1, 2, 3, 4, 5]
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
    $scope.oldInfo = {}
    $scope.newInfo = {}


    $scope.saveChange = function () {
        if (!$scope.commentInput) {
            alert("chưa nhập lý do thay đổi")
            $scope.newInfo = jQuery.extend(true, {}, $scope.oldInfo);
            return;
        }
        data = {}

        data.id = $scope.ticket.id
        if ($scope.oldInfo.status != $scope.newInfo.status) {
            data.status = parseInt($scope.newInfo.status)
            data.note = ("thay đổi trạng thái: " + $filter('toTicketStatus')($scope.oldInfo.status) + "=> " + $filter('toTicketStatus')($scope.newInfo.status) +
                "----- lí do : " + $scope.commentInput)

            $http.put('api/edit-ticket/status', params = data).then(function (response) {
                    alert("thay đổi trạng thái thành công")
                },
                function () {
                    alert("thông tin thay đổi không đúng")
                })
        }
        if ($scope.oldInfo.priority != $scope.newInfo.priority) {
            data.priority = $scope.newInfo.priority
            data.note = $sce.trustAsHtml("thay đổi mức độ ưu tiên: " + $filter('toPriority')($scope.oldInfo.priority) + "=> " + $filter('toPriority')($scope.newInfo.priority) +
                "-----  lí do : " + $scope.commentInput)
            $http.put('api/edit-ticket/priority', params = data).then(function (response) {
                    alert("thay đổi độ ưu tiên thành công")
                    console.log(response)
                },
                function () {
                    alert("thông tin thay đổi không đúng")
                })
        }
        if ($scope.oldInfo.deadline != $scope.newInfo.deadline) {
            data.deadline = $scope.newInfo.deadline
            data.note = ("thay đổi deadline: " + ($scope.oldInfo.deadline).toString() + "=> " + ($scope.newInfo.deadline).toString() +
                "----- lí do : " + $scope.commentInput)
            $http.put('api/edit-ticket/deadline', params = data).then(function (response) {
                    alert("thay đổi deadline thành công")
                },
                function (message) {
                    alert(message)
                })
        }
        if ($scope.oldInfo.related_user != $scope.newInfo.related_user) {
            data.related_user = $scope.newInfo.status
            data.note = ("thay đổi người liên quan: " + $filter('')($scope.oldInfo.status) + "=> " + $filter('')($scope.newInfo.status) +
                "----- lí do : " + $scope.commentInput)
            $http.put('api/edit-ticket/related-user', params = data).then(function (response) {
                    alert("thay đổi người liên quan thành công")
                },
                function () {
                    alert("thông tin thay đổi không đúng")
                })
        }
        if ($scope.oldInfo.team_id != $scope.newInfo.team_id) {
            data.team_id = $scope.newInfo.team_id
            data.note = ("thay đổi đôi IT: " + $filter('toTeam')($scope.oldInfo.team_id) + "=> " + $filter('toTeam')($scope.newInfo.team_id) +
                "----- lí do : " + $scope.commentInput)
            $http.put('api/edit-ticket/team', params = data).then(function (response) {
                    alert("thay đổi đôi IT thành công")
                },
                function () {
                    alert("thông tin thay đổi không đúng")
                })
        }
        $scope.initNewInfo()
    }

    $scope.createComment = function () {
        if (!$scope.commentInput2) {
            alert("nội dung trống")
            return;
        }
        data = {ticket_id: $scope.ticket.id, content: $scope.commentInput}
        commentService.createComment(data)
        // $scope.comments.push( fakeDataService.fakeComments(1,data.id , 1 , data.content)[0])
        // $scope.commentInput = null
    }


    $scope.getStatus = function () {
        return [1, 2, 3, 4, 5, 6]
    }
    $scope.getPriority = function () {
        return [1, 2, 3, 4, 5]
    }
    $scope.changeNewPriority = function (priority) {
        $scope.newInfo.priority = priority
    }
    $scope.changeNewStatus = function (status) {
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

    $http.get("/api/get-ticket/" + $scope.id.toString()).then(function (response) {
        $scope.ticket = new Ticket(response.data)
        $scope.initNewInfo()
    }, function () {
        alert("ticket Id không đúng")
    })


}])


myApp.controller('newRequestController', ['$scope', 'ticketService', '$rootScope', 'commentService', function ($scope, ticketService, $rootScope, commentService) {
    $scope.user = $rootScope.user
    $scope.ticket = new Ticket()
    $scope.ticket.related_user = []
    $scope.ticket.deadline = new Date()

    $scope.save = function () {
        if (!$scope.ticket.priority || !$scope.ticket.content || !$scope.ticket.team_id)
            alert("dữ liệu bị thiếu")
        else {
            ticketService.saveTicket($scope.ticket)
        }
    }

    $scope.loadComment = function () {
    }


}])


myApp.controller('headerController', ['$scope', '$rootScope', function ($scope, $rootScope) {
    $scope.info = $rootScope.info
}])
