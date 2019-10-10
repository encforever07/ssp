<template>
    <div id="usuarios">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div v-if="empty || fulled" class="card">
                    <div class="card-header">
                        <h4 class="card-title">Usuarios</h4>
                        <div class="card-tools alignRight">
                            <button type="button" class="btn btn-info" @click="create" title="Adicionar usuario"><i
                                    class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div v-if="empty" class="row justify-content-center">
                            <td colspan="6">
                                <div class="alert text-center" role="alert">
                                    Aun no existen usuarios registrados al sistema.
                                </div>
                            </td>
                        </div>
                        <div v-if="fulled">
                            <div class="mb-3">
                                <div class="row">
                                    <div class="col-md-2 mb-1">
                                        <strong>Buscar por: </strong>
                                    </div>
                                    <div class="col-md-3 mb-1">
                                        <select v-model="queryField" id="fields" class="form-control"
                                                @change="searchData">
                                            <option value="username" selected>Usuario</option>
                                            <option value="name">Nombre</option>
                                            <option value="email">Correo</option>
                                        </select>
                                    </div>
                                    <div class="input-group col-md-7 mb-1">
                                        <input v-model="query" type="search" class="form-control" placeholder="Buscar">
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                    <tr>
                                        <!--                                    <th scope="col">#</th>-->
                                        <th scope="col">Usuario</th>
                                        <th scope="col">Nombre</th>
                                        <th scope="col">Correo</th>

                                        <th scope="col" class="text-center">Acci&oacute;n</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(usuario, index) in usuarios" :key="usuario.id">
                                        <!--                                    <th scope="row">{{index+1}}</th>-->
                                        <td>{{usuario.username}}</td>
                                        <td>{{usuario.name}}</td>
                                        <td>{{usuario.email}}</td>
                                        <td class="text-center">
                                            <button :disabled="usuario.name=='-' ? true : false" type="button"
                                                    @click="viewData(usuario)"
                                                    class="btn btn-info btn-sm" title="Detalles del usuario">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" @click="edit(usuario)" class="btn btn-primary btn-sm"
                                                    title="Editar usuario">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" @click="destroy(usuario)"
                                                    class="btn btn-danger btn-sm" title="Eliminar usuario">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </td>
                                    </tr>

                                    <tr v-if="usuarios.length == 0 && searched">
                                        <td colspan="6">
                                            <div class="alert text-center text-danger" role="alert">
                                                <b class="errorlist">Ning&uacute;n elemento coincide con el criterio de
                                                    búsqueda.</b>
                                            </div>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <pagination v-if="pagination.last_page > 1"
                                    :pagination="pagination"
                                    :offset="5"
                                    @paginate="query === ''? getUsuarios():searchData()"
                        ></pagination>
                    </div>
                </div>
            </div>
            <vue-progress-bar></vue-progress-bar>
            <vue-snotify></vue-snotify>

        </div>

        <div class="overflow-auto:scroll"></div>
        <!--Modal-->
        <div class="modal fade" id="usuarioModalLong" tabindex="-1" role="dialog"
             aria-labelledby="usuarioModalLongTitle" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 v-if="!viewMode" class="modal-title" id="usuarioModalLongTitle">
                            {{editMode ? 'Editando usuario '+form.username:'Adicionar usuario'}}</h5>
                        <h5 class="modal-title" id="usuarioModalLongTitle">
                            {{viewMode ? 'Detalles del usuario '+form.username:''}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div v-if="storeMode">
                        <form @submit.prevent="store()" @keydown="form.onKeydown($event)">
                            <div class="modal-body">
                                <!--                            <alert-error :form="form"></alert-error>-->
                                <div class="form-group">
                                    <label>Usuario</label>
                                    <input v-model="form.username" type="text" name="username"
                                           class="form-control" :class="{ 'is-invalid': form.errors.has('username') }">
                                    <has-error :form="form" field="username"></has-error>
                                </div>
                                <div class="form-group">
                                    <label>Rol</label>
                                    <select v-model="form.rol" class="form-control">
                                        <!--                                        <option value="" disabled>Seleccione...</option>-->
                                        <option v-for="rol in roles" v-bind:value="rol.nombre">
                                            {{rol.nombre}}
                                        </option>
                                    </select>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Salir</button>
                                    <button :disabled="fullData" type="submit" class="btn btn-primary">Guardar
                                    </button>
                                </div>
                            </div>

                        </form>
                    </div>
                    <div v-if="editMode">
                        <form @submit.prevent="update()" @keydown="form.onKeydown($event)">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Activaci&oacute;n</label>
                                    <input type="checkbox" id="isActive" v-model="form.isActive" :value=form.isActive>
                                </div>
                                <div class="form-group">
                                    <label>Rol</label>
                                    <select v-model="form.rol" class="form-control">
                                        <option v-for="rol in roles" :value=rol.nombre>
                                            {{rol.nombre}}
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Detalles</label>
                                    <textarea v-model="form.detail" class="form-control"
                                              placeholder="Detalles"></textarea>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Salir</button>
                                    <button :disabled="fullData" type="submit" class="btn btn-primary">Guardar
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div v-if="viewMode">
                        <div class="modal-body">
                            <div class="col-md-12">
                                <blockquote>
                                    <h5>{{usuarioAver.name}}</h5>
                                    <small><cite title="Source Title">{{usuarioAver.department}}, {{usuarioAver.office}}
                                        <i class="icon-map-marker"></i></cite></small>
                                </blockquote>
                                <div class="col-m12">
                                    <table class="table-responsive border-0 row-">
                                        <tr>
                                            <th>Usuario:</th>
                                            <td>{{usuarioAver.username}}</td>
                                        </tr>
                                        <tr>
                                            <th>Correo:</th>
                                            <td>{{usuarioAver.email}}</td>
                                        </tr>
                                        <tr>
                                            <th>Rol:</th>
                                            <td>{{usuarioAver.rol}}</td>
                                        </tr>
                                        <tr>
                                            <th>Estado:</th>
                                            <td>{{usuarioAver.isActive===true?'Activo':'Inactivo'}}</td>
                                        </tr>
                                        <tr v-if="usuarioAver.detail!=null">
                                            <th>Detalles:</th>
                                            <td>{{usuarioAver.detail}}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Salir</button>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
        <!--EndModal-->
    </div>

</template>

<script>

    export default {
        data() {
            return {
                storeMode: false,
                editMode: false,
                viewMode: false,
                query: '',
                queryField: 'username',
                searched: '',
                empty: false,
                fulled: false,
                usuarioAver: '',
                usuarios: [],
                roles: [],
                usuarioAeditar: {
                    name: '',
                    username: '',
                    rol: '',
                    isActive: '',
                    detail: ''
                },
                form: new Form({
                    id: '',
                    username: '',
                    rol: 'Admin',
                    busy: false,
                    isActive: '',
                    detail: ''
                }),
                pagination: {
                    current_page: 1,
                },
                showModal: false

            }
        },
        watch: {
            query: function (newQ, old) {
                if (newQ === '') {
                    this.getUsuarios()
                } else {
                    this.searchData()
                }
            }
        },
        created() {
        },
        mounted() {
            this.getRols()
            this.getUsuarios()
        },
        computed: {
            fullData() {
                if (this.editMode) {
                    if (this.usuarioAeditar.isActive === this.form.isActive && this.usuarioAeditar.rol === this.form.rol && this.usuarioAeditar.detail === this.form.detail) {
                        return true
                    } else {
                        return false
                    }

                }
                if (this.form.username === '' || this.form.rol === '')
                    return true

                return false
            }
        },
        methods: {
            setFocus: function () {
                // Note, you need to add a ref="search" attribute to your input.
                this.$refs.namefocus.focus();
            },
            getRols() {
                axios.get('roles')
                    .then(response => {
                        this.roles = response.data
                    })
            },
            getUsuarios: function () {
                let urlUsuarios = 'usuarios?page=' + this.pagination.current_page
                this.$Progress.start()
                axios.get(urlUsuarios).then(response => {
                    this.usuarios = response.data.usuarios.data
                    this.empty = this.usuarios.length == 0 ? true : false
                    this.fulled = !this.empty
                    this.pagination = response.data.usuarios
                    this.searched = ''
                    this.$Progress.finish()
                })
                    .catch(e => {
                        console.log(e)
                        this.$Progress.fail()
                    })
            },
            searchData() {
                if (this.query !== '') {
                    this.$Progress.start()
                    axios.get('search/usuarios/' + this.queryField + '/' + this.query + '?page=' + this.pagination.current_page)
                        .then(response => {
                            this.usuarios = response.data.data
                            this.pagination = response.data
                            this.searched = 'true'
                            this.$Progress.finish()
                        })
                        .catch(e => {
                            this.$Progress.fail()
                        })
                }


            },
            reload() {
                this.getUsuarios()
                this.query = ''
                this.queryField = 'username'
                this.searched = ''
                this.$snotify.success('Pagina Cargada satisfactoriamente')

            },
            create() {
                this.form.busy = true
                this.storeMode = true
                this.editMode = false
                this.viewMode = false
                this.form.reset()
                this.form.clear
                $('#usuarioModalLong').modal('show');
            },
            store() {
                this.$Progress.start()
                this.form.busy = true
                this.form.post('usuarios')
                    .then(response => {
                        console.log(response)
                        this.getUsuarios()
                        if (this.form.successful) {
                            this.form.reset()
                            this.form.clear()
                            this.$Progress.finish()
                            this.$snotify.success('Usuario Adicionado Satisfactoriamente')
                        } else {
                            this.$Progress.fail()
                            this.$snotify.error('Hay algun error. Compruebe al administrador de red')
                        }

                    })
                    .catch(e => {
                        let {response} = e;
                        if (response && response.status === 404) {
                            this.$Progress.fail()
                            this.$snotify.error('El nombre de usuario no existe en el dominio. Verifíquelo')
                        } else {
                            this.$Progress.fail()
                            this.$snotify.error('El usuario ya existe en el sistema o ha ocurrido algun problema. Consulte al Administrador')

                        }


                    })

            },
            edit(usuario) {
                this.storeMode = false
                this.form.reset()
                this.form.clear()
                this.form.fill(usuario)
                this.editMode = true
                this.viewMode = false
                this.usuarioAeditar.isActive = usuario.isActive
                this.usuarioAeditar.rol = usuario.rol
                this.usuarioAeditar.detail = usuario.detail
                $('#usuarioModalLong').modal('show');
            },
            update() {
                this.$Progress.start()
                this.form.busy = true
                this.form.put('usuarios/' + this.form.id).then(response => {
                    this.getUsuarios()
                    $('#usuarioModalLong').modal('hide')
                    if (this.form.successful) {
                        this.$Progress.finish()
                        this.$snotify.success('Usuario editado Satisfactoriamente')
                    } else {
                        this.$Progress.fail()
                        this.$snotify.error('Hay algun error. Compruebe al administrador de red')
                    }

                })
                    .catch(e => {
                        this.$Progress.fail()
                        this.$snotify.error('El usuario existe o necesita editar algun valor')
                    })
            },
            viewData(usuario) {
                this.storeMode = false
                this.editMode = false
                this.viewMode = true
                this.usuarioAver = usuario
                $('#usuarioModalLong').modal('show');

            },
            destroy(usuario) {
                this.$snotify.clear
                this.$snotify.confirm(
                    "No volverás a recuperar los datos del usuario!!",
                    "Estás seguro?",
                    {
                        showProgressBar: false,
                        closeOnClick: false,
                        pauseOnHover: true,
                        buttons: [
                            {
                                text: "Sí",
                                action: toast => {
                                    this.$snotify.remove(toast.id)
                                    this.$Progress.start()
                                    axios.delete('usuarios/' + usuario.id)
                                        .then(response => {
                                            if (this.pagination.data.length == 1) {
                                                this.pagination.current_page = this.pagination.last_page - 1
                                            }
                                            this.getUsuarios()
                                            this.$Progress.finish()
                                            this.$snotify.success("Usuario eliminado satisfactoriamente",
                                                "Correcto"
                                            )
                                        })
                                },
                                bold: true
                            },
                            {
                                text: "No",
                                action: toast => {
                                    this.$snotify.remove(toast.id)
                                },
                                bold: true
                            }
                        ]
                    }
                )
            }

        },

    }
</script>

<style scoped>
    .modal-mask {
        position: fixed;
        z-index: 9998;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, .5);
        display: table;
        transition: opacity .3s ease;
    }

    .errorlist {
        color: grey;
    }

    .modal-wrapper {
        display: table-cell;
        vertical-align: middle;
    }

    .alignRight {
        position: absolute;
        right: 1rem;
        top: .5rem;
    }
</style>
