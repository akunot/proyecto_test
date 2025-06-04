from flask import Flask, request, jsonify
import joblib
import firebase_admin
from firebase_admin import credentials, firestore
from functools import wraps


app = Flask(__name__)

# API Key 
VALID_API_KEY = "e2c679c2-37d0-4fbf-a2b7-5382ce2f90ac"

# Decorador para verificar la API Key
def require_api_key(f):
    @wraps(f)
    def wrapper(*args, **kwargs):
        api_key = request.headers.get("x-api-key")
        if api_key != VALID_API_KEY:
            return jsonify({"error": "No autorizado"}), 401
        return f(*args, **kwargs)
    return wrapper

# Inicializar Firebase
cred = credentials.Certificate("env.json")
firebase_admin.initialize_app(cred)
db = firestore.client()

# Cargar modelo y vectorizador
modelo = joblib.load("sentiment_model.pkl")
vectorizer = joblib.load("vectorizer.pkl")

@app.route('/')
@require_api_key
def hello_world():
    return 'Hello, Routes(obtener_predicciones, predecir)!'

#OBTENER TODAS LAS PREDICCIONES (GET)
@app.route('/obtener_predicciones', methods=['GET'])
@require_api_key
def obtener_predicciones():
    try:
        predicciones_ref = db.collection('predicciones')
        docs = predicciones_ref.stream()

        predicciones = [{"id": doc.id, **doc.to_dict()} for doc in docs]

        return jsonify(predicciones), 200
    except Exception as e:
        return jsonify({"error": str(e)}), 500

#OBTENER UNA PREDICCIÓN POR ID (READ ONE)
@app.route('/prediccion/<id>', methods=['GET'])
@require_api_key
def obtener_prediccion(id):
    try:
        doc_ref = db.collection('predicciones').document(id)
        doc = doc_ref.get()
        
        if not doc.exists:
            return jsonify({"error": "Predicción no encontrada"}), 404
        
        return jsonify({"id": doc.id, **doc.to_dict()}), 200
    except Exception as e:
        return jsonify({"error": str(e)}), 500

#CREAR UNA PREDICCIÓN (CREATE)
@app.route("/predecir", methods=["POST"])
@require_api_key
def predecir():
    try:
        datos = request.json
        texto = datos.get("texto", "")

        if not texto:
            return jsonify({"error": "Texto no proporcionado"}), 400

        # Transformar el texto usando el vectorizador
        texto_vectorizado = vectorizer.transform([texto])

        # Hacer la predicción
        prediccion = modelo.predict(texto_vectorizado)[0]

        sentimientos = {0: "Neutral", 1: "Positivo", 2: "Negativo"}
        sentimiento = sentimientos.get(int(prediccion), "Desconocido")

        # Guardar en Firebase
        doc_ref = db.collection("predicciones").add({
            "texto": texto,
            "sentimiento": sentimiento
        })

        return jsonify({"id": doc_ref[1].id, "texto": texto, "sentimiento": sentimiento}), 201
    except Exception as e:
        return jsonify({"error": str(e)}), 500

#ACTUALIZAR UNA PREDICCIÓN (UPDATE)
@app.route('/prediccion/<id>', methods=['PUT'])
@require_api_key
def actualizar_prediccion(id):
    try:
        datos = request.json
        texto = datos.get("texto", "")

        if not texto:
            return jsonify({"error": "Texto no proporcionado"}), 400

        # Transformar el texto usando el vectorizador
        texto_vectorizado = vectorizer.transform([texto])

        # Hacer la predicción
        prediccion = modelo.predict(texto_vectorizado)[0]

        sentimientos = {0: "Neutral", 1: "Positivo", 2: "Negativo"}
        sentimiento = sentimientos.get(int(prediccion), "Desconocido")

        # Actualizar en Firebase
        doc_ref = db.collection("predicciones").document(id)
        if not doc_ref.get().exists:
            return jsonify({"error": "Predicción no encontrada"}), 404
        
        doc_ref.update({"texto": texto, "sentimiento": sentimiento})

        return jsonify({"id": id, "texto": texto, "sentimiento": sentimiento}), 200
    except Exception as e:
        return jsonify({"error": str(e)}), 500

#ELIMINAR UNA PREDICCIÓN (DELETE)
@app.route('/prediccion/<id>', methods=['DELETE'])
@require_api_key
def eliminar_prediccion(id):
    try:
        doc_ref = db.collection("predicciones").document(id)
        
        if not doc_ref.get().exists:
            return jsonify({"error": "Predicción no encontrada"}), 404
        
        doc_ref.delete()
        
        return jsonify({"mensaje": "Predicción eliminada correctamente"}), 200
    except Exception as e:
        return jsonify({"error": str(e)}), 500

if __name__ == '__main__':
    app.run(debug=True)
