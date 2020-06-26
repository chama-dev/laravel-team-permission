# Laravel Team Permission
This package give ability to set permissions to each member of your team.

This is not a plug and play package and you need to configure acoording to your need.

This is not a ACL package, is just to control routes and the Team Object.

## Assumptions
All classes must be extended to avoid decentralized import.

At user scope we have three main roles.
- Master: A registered user that can do anything;
- Registered: A registered user;
- Guest: Everybody that is disconnected. 

This package only handles with authenticated users, that means you decide what has public access in your application.

By default, if a route is not defined, it will be blocked. 

### Grant Exceptions
You can give access to specific routes and models to a specific user.

### Deny Excpetions
You can deny access to specific routes and models to a specific user.
 
### Models
Your "Team" model has two main attributes:
 - owner_id: The user that owns
 - name: Readable name for your "Team"

### Middleware
It's here that magic happens

### User Class
Como o time pode ser qualquer modelo, você precisa definir quais models se comportam como times

```
    public function ownedGyms()
    {
        return $this->gyms()->where('owner_id', '=', $this->getKey());
    }
```
### Team Role Class
Extender o modelo TeamRole , como no cenário de testes organizei diferente
tive que setar o nome da tabela.
```
class Role extends TeamRole
{

    protected $table = 'team_roles';

```

No meu caso o usuário pode ser dono de várias academias

### Passos
1 - Criar Papéis (team_role)
2 - Associar o usuário a um papel (team_member)
3 - Desassociar um usuário de um papel

Implementar regras de validação por etapas
1 - Está no time
2 - O seu papel no time tem permissão
3 - Tem alguma regra global no papel permitindo ou negando algo
4 - Tem alguma regra relacionada ao usuário permitingo ou negando algo
5 - Tem algum intervalo de liberação
14 - Convidar um usuário por um link assinado
