<style scoped>
    .action-link {
        cursor: pointer;
    }
</style>

<template>
    <div>
        <div>
            <div class="card card-default">
                <div class="card-header">
                    <div style="display: flex; justify-content: space-between; align-items: center;">

                        <h2>
                            Personal Access Tokens - <span class="badge badge-info">{{tokens.length}}</span>
                        </h2>

                        <button tabindex="-1" title="Add New" type="button" aria-label="Add New" class="btn btn-default" @click="showCreateTokenForm">
                            <i aria-hidden="true" class="fa fa-plus"></i> Create New Token
                        </button>

                    </div>
                </div>

                <div class="card-body">
                    <!-- No Tokens Notice -->
                    <p class="mb-0 text-warning" v-if="tokens.length === 0">
                        You have not created any personal access tokens.
                    </p>

                    <!-- Personal Access Tokens -->
                    <table class="table table-borderless mb-0" v-if="tokens.length > 0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th width="250"></th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="token in tokens">
                                <!-- Client Name -->
                                <td style="vertical-align: middle;">
                                    {{ token.name }}
                                </td>

                                <!-- Delete Button -->
                                <td style="vertical-align: middle; padding-right: 10px" align="right">
                                    <a class="action-link text-danger" @click="revoke(token)">
                                        Delete
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Create Token Modal -->
        <modal-box ref="modalCreateTokenPA" data-show-footer="true">
            <div slot="title">
                Create Token
            </div>
            <div slot="content">
                <div class="alert alert-danger" v-if="form.errors.length > 0">
                    <p class="mb-0"><strong>Whoops!</strong> Something went wrong!</p>
                    <br>
                    <ul>
                        <li v-for="error in form.errors">
                            {{ error }}
                        </li>
                    </ul>
                </div>

                <!-- Create Token Form -->
                <form role="form" @submit.prevent="store">
                    <!-- Name -->
                    <div class="form-group row">
                        <label class="col-md-4 col-form-label">Name</label>

                        <div class="col-md-6">
                            <input type="text" class="form-control" name="name" v-model="form.name">
                        </div>
                    </div>

                    <!-- Scopes -->
                    <div class="form-group row" v-if="scopes.length > 0">
                        <label class="col-md-4 col-form-label">Scopes</label>

                        <div class="col-md-6">
                            <div v-for="scope in scopes">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox"
                                               @click="toggleScope(scope.id)"
                                               :checked="scopeIsAssigned(scope.id)">

                                        {{ scope.id }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div slot="footer" class="center-align">
                <button type="button" class="btn btn-primary" @click="store">
                    Create
                </button>
            </div>
        </modal-box>

        <modal-box ref="modalAccessTokenPA" data-show-footer="true">
            <div slot="title">
                Personal Access Token
            </div>
            <div slot="content">
                <p class="text-danger">
                    Here is your new personal access token. <strong>This is the only time it will be shown so don't lose it!</strong>
                    You may now use this token to make API requests.
                </p>

                <textarea class="form-control" rows="10">{{ accessToken }}</textarea>

            </div>

        </modal-box>

    </div>
</template>

<script>
    import {Toast, Modal, Loader} from '../../helpers/Common';
    export default {
        /*
         * The component's data.
         */
        data() {
            return {
                accessToken: null,

                tokens: [],
                scopes: [],

                form: {
                    name: '',
                    scopes: [],
                    errors: []
                }
            };
        },

        /**
         * Prepare the component (Vue 1.x).
         */
        ready() {
            this.prepareComponent();
        },

        /**
         * Prepare the component (Vue 2.x).
         */
        mounted() {
            this.prepareComponent();
        },

        methods: {
            /**
             * Prepare the component.
             */
            prepareComponent() {
                this.getTokens();
                this.getScopes();

               /* $('#modal-create-token').on('shown.bs.modal', () => {
                    $('#create-token-name').focus();
                });*/
            },

            /**
             * Get all of the personal access tokens for the user.
             */
            getTokens() {
                axios.get('/oauth/personal-access-tokens')
                        .then(response => {
                            this.tokens = response.data;
                        });
            },

            /**
             * Get all of the available scopes.
             */
            getScopes() {
                axios.get('/oauth/scopes')
                        .then(response => {
                            this.scopes = response.data;
                        });
            },

            /**
             * Show the form for creating new tokens.
             */
            showCreateTokenForm() {
                Modal.open(this, "modalCreateTokenPA")
            },

            /**
             * Create a new personal access token.
             */
            store() {
                this.accessToken = null;

                this.form.errors = [];

                axios.post('/oauth/personal-access-tokens', this.form)
                        .then(response => {
                            this.form.name = '';
                            this.form.scopes = [];
                            this.form.errors = [];

                            this.tokens.push(response.data.token);

                            this.showAccessToken(response.data.accessToken);
                        })
                        .catch(error => {
                            if (typeof error.response.data === 'object') {
                                this.form.errors = _.flatten(_.toArray(error.response.data.errors));
                            } else {
                                this.form.errors = ['Something went wrong. Please try again.'];
                            }
                        });
            },

            /**
             * Toggle the given scope in the list of assigned scopes.
             */
            toggleScope(scope) {
                if (this.scopeIsAssigned(scope)) {
                    this.form.scopes = _.reject(this.form.scopes, s => s == scope);
                } else {
                    this.form.scopes.push(scope);
                }
            },

            /**
             * Determine if the given scope has been assigned to the token.
             */
            scopeIsAssigned(scope) {
                return _.indexOf(this.form.scopes, scope) >= 0;
            },

            /**
             * Show the given access token to the user.
             */
            showAccessToken(accessToken) {

                this.accessToken = accessToken;
                Modal.close(this, "modalCreateTokenPA");
                Modal.open(this, "modalAccessTokenPA");
            },

            /**
             * Revoke the given token.
             */
            revoke(token) {
                axios.delete('/oauth/personal-access-tokens/' + token.id)
                        .then(response => {
                            this.getTokens();
                        });
            }
        }
    }
</script>
