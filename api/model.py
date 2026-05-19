import pickle
import pandas as pd
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.naive_bayes import MultinomialNB
from sklearn.model_selection import train_test_split
from sklearn.metrics import accuracy_score, classification_report
import os
from preprocessing import TextPreprocessor

class ChatbotModel:
    def __init__(self, model_path='model.pkl', vectorizer_path='vectorizer.pkl'):
        self.model_path = model_path
        self.vectorizer_path = vectorizer_path
        self.model = None
        self.vectorizer = None
        self.preprocessor = TextPreprocessor()

    def load(self):
        if os.path.exists(self.model_path) and os.path.exists(self.vectorizer_path):
            with open(self.model_path, 'rb') as f:
                self.model = pickle.load(f)
            with open(self.vectorizer_path, 'rb') as f:
                self.vectorizer = pickle.load(f)
            return True
        return False

    def save(self):
        with open(self.model_path, 'wb') as f:
            pickle.dump(self.model, f)
        with open(self.vectorizer_path, 'wb') as f:
            pickle.dump(self.vectorizer, f)

    def train(self, dataset_path):
        df = pd.read_csv(dataset_path)

        if df.empty:
            return {'error': 'Dataset is empty'}, False

        if 'original_text' not in df.columns or 'category_name' not in df.columns:
            return {'error': 'Dataset must have columns: original_text, category_name'}, False

        df['cleaned_text'] = df['original_text'].apply(self.preprocessor.preprocess)

        X = df['cleaned_text']
        y = df['category_name']

        X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)

        self.vectorizer = TfidfVectorizer(max_features=5000, ngram_range=(1, 2))
        X_train_tfidf = self.vectorizer.fit_transform(X_train)
        X_test_tfidf = self.vectorizer.transform(X_test)

        self.model = MultinomialNB()
        self.model.fit(X_train_tfidf, y_train)

        y_pred = self.model.predict(X_test_tfidf)
        accuracy = accuracy_score(y_test, y_pred)
        report = classification_report(y_test, y_pred, output_dict=True)

        self.save()

        return {
            'accuracy': round(accuracy * 100, 2),
            'classification_report': report
        }, True

    def predict(self, text):
        if self.model is None or self.vectorizer is None:
            return None, None

        cleaned_text = self.preprocessor.preprocess(text)
        text_tfidf = self.vectorizer.transform([cleaned_text])

        prediction = self.model.predict(text_tfidf)[0]
        probabilities = self.model.predict_proba(text_tfidf)[0]
        confidence = round(max(probabilities) * 100, 2)

        return prediction, confidence
