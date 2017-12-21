function Ticket(obj){
    this.index        = null
    this.id           = null
    this.subject      = null
    this.content      = null
    this.create_by    = null
    this.status       = null
    this.priority     = null
    this.deadline     = new Date(0,0,0,0,0,0)
    this.assigned_to  = null
    this.rating       = null
    this.team_id      = null
    this.resolved_at  = null
    this.closed_at    = new Date(0,0,0,0,0,0)
    this.created_at   = new Date()
    this.updated_at   = null
    this.deleted_at   = null
    this.is_read      = null
    this.image        = null
    for (var pro in obj)  this[pro] = obj[pro]
}

function Comment(obj){
    this.id         = null
    this.ticket_id  = null
    this.create_at  = null
    this.user_name   = null
    this.user_id    = null
    this.content    = null
    this.image      = null
    this.created_at = null
}




function Condition(obj){
    this.id             = null
    this.subject        = null
    this.create_by      = null
    this.status         = null
    this.priority       = null
    this.employee_id    = null
    this.deadline       = Date.UTC()
    this.mainCondition  = null
    this.related_user_id = null
    this.id_user_team = null
}

function TicketThread(obj){
    this.id          = null
    this.ticket_id   = null
    this.employee_id = null
    this.content     = null
    this.type        = null
    this.note        = null
    this.created_at  = null
    this.updated_at  = null
    for (var pro in obj)
      this[pro] = obj[pro]
}

function TicketImage(obj){
    this.id_ticket   = null
    this.url_image   = null
    for (var pro in obj) 
         this[pro] = obj[pro]
}

function TicketRead(obj){
    this.ticket_id  = null
    this.status     = null
    for (var pro in obj)  
        this[pro] = obj[pro]
}

function TicketRelated(obj){
    this.ticket_id       = null
    this.employee_id     = null
    for (var pro in obj) 
      this[pro] = obj[pro]
}

function TicketAttribute(obj){
    this.id         = null
    this.status     = null
    this.priority   = null
    this.rating     = null
    this.reopened   = null
    for (var pro in obj) 
       this[pro] = obj[pro]
}


function PaginatePrams(){
    this.current_page = 1
    this.page_size = 10
    this.total = 10
}


function User(obj){
    this.id         = null
    this.username   = null
    this.email      = null
    this.birthday   = null
    this.gender     = null
    for (var pro in obj) 
        this[pro] = obj[pro]
}
