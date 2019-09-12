pipeline {
    agent { label 'jworker' }

    stages {
        stage('Build') {
            environment {
                RUN_TESTS = true
            }
            steps {
                ansiColor('xterm') {
                    sh './ci/run.sh'
                }
            }
        }
    }

    post {
        cleanup {
            cleanWs()
        }
    }
}
