pipeline {
    agent any

    options {
        skipDefaultCheckout(true)
    }

    environment {
        FIREBASE_ENV = credentials('firebase-env-json')
        DOTENV = credentials('laravel-dotenv')
        EMAIL_ENV = credentials('email-dotenv')
    }

    stages {
        stage('Limpiar workspace') {
            steps {
                deleteDir()
            }
        }

        stage('Clonar proyecto principal') {
            steps {
                git credentialsId: 'github-token', url: 'https://github.com/akunot/proyecto_test', branch: 'main'
            }
        }

        stage('Copiar archivo .env desde secreto') {
            steps {
                sh 'cp "$DOTENV" .env'
            }
        }

        stage('Copiar archivo .env de Email desde secreto') {
            steps {
                sh 'cp "$EMAIL_ENV" .env_email'
            }
        }

        stage('Copiar archivo env_json') {
            steps {
                sh '''
                    chmod -R u+w Flask/Flask_microservice
                    cp "$FIREBASE_ENV" Flask/Flask_microservice/
                '''
            }
        }

        stage('Construir contenedores') {
            steps {
                sh 'docker compose build'
            }
        }

        stage('Levantar servicios') {
            steps {
                sh 'docker compose up -d'
            }
        }

        stage('Instalar dependencias') {
            steps {
                sh '''
                    docker compose exec laravel composer install
                    docker compose exec email composer install
                    docker compose exec -T flask pip install -r requirements.txt
                '''
            }
        }

        stage('Copiar .env a Laravel') {
            steps {
                sh "docker compose cp .env laravel:/var/www/html/.env"
            }
        }

        stage('Copiar .env a Email') {
            steps {
                sh "docker compose cp .env_email email:/var/www/html/.env"
            }
        }

        stage('Esperar MySQL') {
            steps {
                sh '''
                    docker compose exec mysql bash -c '
                        for i in {1..10}; do
                            if mysqladmin ping -h localhost --silent; then
                                exit 0
                            fi
                            sleep 5
                        done
                        exit 1
                    '
                '''
            }
        }

        stage('Migrar Base de Datos') {
            steps {
                sh '''
                    docker compose exec laravel php artisan key:generate
                    docker compose exec laravel php artisan migrate --force
                '''
            }
        }

        stage('Ejecutar pruebas') {
            steps {
                sh '''
                    docker compose exec laravel php artisan test
                    docker compose exec email php artisan test
                '''
            }
        }
    }

    post {
        always {
            sh 'docker compose down'
        }
    }
}