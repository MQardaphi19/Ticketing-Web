import re
from Sastrawi.Stemmer.StemmerFactory import StemmerFactory

class TextPreprocessor:
    def __init__(self):
        factory = StemmerFactory()
        self.stemmer = factory.create_stemmer()
        self.stopwords = self._load_stopwords()

    def _load_stopwords(self):
        stopwords = set([
            'yang', 'untuk', 'pada', 'ke', 'kepada', 'oleh', 'dengan', 'dari',
            'di', 'dan', 'atau', 'tetapi', 'karena', 'sebagai', 'adalah',
            'ini', 'itu', 'tersebut', 'mereka', 'kita', 'saya', 'anda',
            'tidak', 'bisa', 'dapat', 'ada', 'telah', 'sudah', 'akan',
            'lebih', 'sangat', 'dalam', 'antara', 'melalui', 'sekitar',
            'setiap', 'semua', 'beberapa', 'banyak', 'sedikit', 'hanya',
            'juga', 'yaitu', 'yakni', 'adanya', 'memang', 'tetap',
            'lagi', 'masih', 'jika', 'jika', 'bila', 'apabila', 'ketika',
            'saat', 'setelah', 'sebelum', 'sampai', 'hingga', 'hingga',
            'bagi', 'tentang', 'terhadap', 'menurut', 'selain', 'kecuali',
            'seperti', 'layaknya', 'serupa', 'misalnya', 'contohnya'
        ])
        return stopwords

    def case_folding(self, text):
        return text.lower()

    def remove_special_chars(self, text):
        text = re.sub(r'[^\w\s]', ' ', text)
        text = re.sub(r'\d+', ' ', text)
        text = re.sub(r'\s+', ' ', text).strip()
        return text

    def tokenize(self, text):
        return text.split()

    def remove_stopwords(self, tokens):
        return [token for token in tokens if token not in self.stopwords]

    def stemming(self, tokens):
        return [self.stemmer.stem(token) for token in tokens]

    def preprocess(self, text):
        text = self.case_folding(text)
        text = self.remove_special_chars(text)
        tokens = self.tokenize(text)
        tokens = self.remove_stopwords(tokens)
        tokens = self.stemming(tokens)
        return ' '.join(tokens)
