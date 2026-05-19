from fastapi import FastAPI, HTTPException, UploadFile, File
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
import os
from model import ChatbotModel

app = FastAPI(title="Chatbot Naive Bayes API", version="1.0.0")

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

chatbot_model = ChatbotModel()
chatbot_model.load()

class PredictRequest(BaseModel):
    text: str

@app.get("/")
async def root():
    return {
        "message": "Chatbot Naive Bayes API",
        "version": "1.0.0",
        "endpoints": {
            "/predict": "POST - Predict category from text",
            "/train": "POST - Train model from CSV file",
            "/status": "GET - Check model status"
        }
    }

@app.get("/status")
async def status():
    model_loaded = chatbot_model.model is not None and chatbot_model.vectorizer is not None

    return {
        "status": "ready" if model_loaded else "not_trained",
        "model_loaded": model_loaded,
        "model_path": chatbot_model.model_path,
        "vectorizer_path": chatbot_model.vectorizer_path
    }

@app.post("/predict")
async def predict(request: PredictRequest):
    if chatbot_model.model is None or chatbot_model.vectorizer is None:
        raise HTTPException(
            status_code=400,
            detail="Model not trained. Please train the model first."
        )

    prediction, confidence = chatbot_model.predict(request.text)

    if prediction is None:
        raise HTTPException(status_code=500, detail="Prediction failed")

    # Mapping kategori ke ID database
    CATEGORY_MAPPING = {
        "software": 1,
        "hardware": 2,
        "infrastruktur it": 3,
        "akses": 4,
        "jaringan": 5,
        "lainnya": 6
    }

    # ubah prediction menjadi lowercase
    category_name = prediction.lower()

    # ambil id kategori
    category_id = CATEGORY_MAPPING.get(category_name, 6)

    return {
        "category_id": category_id,
        "category_name": prediction,
        "confidence_score": round(confidence, 2)
    }
@app.post("/train")
async def train(file: UploadFile = File(...)):
    if not file.filename.endswith('.csv'):
        raise HTTPException(status_code=400, detail="File must be a CSV")

    dataset_path = f"temp_{file.filename}"

    try:
        with open(dataset_path, "wb") as buffer:
            content = await file.read()
            buffer.write(content)

        result, success = chatbot_model.train(dataset_path)

        if not success:
            raise HTTPException(status_code=400, detail=result.get('error', 'Training failed'))

        os.remove(dataset_path)

        return {
            "message": "Model trained successfully",
            "accuracy": result['accuracy'],
            "details": result['classification_report']
        }

    except Exception as e:
        if os.path.exists(dataset_path):
            os.remove(dataset_path)
        raise HTTPException(status_code=500, detail=str(e))

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=8001)
